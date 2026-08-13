<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PatientReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class PatientReportController extends Controller
{
    /**
     * Get list of reports for the authenticated patient with stats, pagination and status filter
     * Query params: status (all|Processing|Processed|Failed, default: all), page, per_page (default: 10)
     */
    public function index(Request $request)
    {
        $patient = $request->user();

        $status = $request->input('status', 'all');
        if (!in_array($status, ['all', 'Processing', 'Processed', 'Failed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status filter. Allowed values: all, Processing, Processed, Failed',
            ], 422);
        }

        $perPage = (int) $request->input('per_page', 10);

        $query = PatientReport::where('patient_id', $patient->patient_id)
            ->orderBy('patient_report_id', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $reports = $query->paginate($perPage);

        $allReports = PatientReport::where('patient_id', $patient->patient_id)->get();

        $totalReports = $allReports->count();
        $totalAbnormal = $allReports->sum('abnormal_count');
        $totalMarkersOk = $allReports->sum('ok_count');

        return response()->json([
            'success' => true,
            'message' => 'Reports list fetched successfully',
            'data' => [
                'stats' => [
                    'total_reports' => $totalReports,
                    'total_abnormal' => $totalAbnormal,
                    'total_markers_ok' => $totalMarkersOk,
                ],
                'reports' => $reports
            ]
        ], 200);
    }

    /**
     * Upload a new report for the authenticated patient
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,xlsx,xls|max:20480', // Max 20MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $patient = $request->user();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileSize = $file->getSize();
            $originalName = $file->getClientOriginalName();
            $filename = 'REP-' . time() . '_' . $originalName;

            // Move file to public/uploads/report
            $file->move(public_path('uploads/report'), $filename);

            // Create patient report record initially in Processing status
            $report = PatientReport::create([
                'patient_id' => $patient->patient_id,
                'file_name' => $originalName,
                'file_path' => $filename,
                'file_size' => $fileSize,
                'status' => 'Processing',
            ]);

            try {
                // Call the Sensify Care OCR extraction API
                $response = Http::timeout(60)->post('http://api.sensifycare.com/nodeapp/extract-data', [
                    'file_url' => asset('uploads/report/' . $filename),
                    'language' => $patient->preferred_language ?: 'en'
                ]);

                if ($response->successful()) {
                    $ocrResult = $response->json();
                    
                    $reportTitle = $ocrResult['report_title'] ?? $ocrResult['lab_name'] ?? str_replace(['.pdf', '.png', '.jpg', '.jpeg', '.xlsx', '.xls'], '', $originalName);
                    $score = $ocrResult['score'] ?? null;
                    $markersCount = $ocrResult['markers_count'] ?? (isset($ocrResult['markers']) ? count($ocrResult['markers']) : null);
                    $abnormalCount = $ocrResult['abnormal_count'] ?? null;
                    $okCount = $ocrResult['ok_count'] ?? null;
                    $pagesCount = $ocrResult['pages_count'] ?? null;
                    $reportQuality = $ocrResult['report_quality'] ?? null;

                    $report->update([
                        'status' => 'Processed',
                        'ocr_data' => $ocrResult,
                        'report_title' => $reportTitle,
                        'score' => $score,
                        'markers_count' => $markersCount,
                        'abnormal_count' => $abnormalCount,
                        'ok_count' => $okCount,
                        'pages_count' => $pagesCount,
                        'report_quality' => $reportQuality,
                    ]);
                } else {
                    $report->update([
                        'status' => 'Failed',
                    ]);
                }
            } catch (\Exception $e) {
                $report->update([
                    'status' => 'Failed',
                ]);
            }

            // Refresh model instance to get updated status and ocr_data attributes
            $report->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Report uploaded and processed successfully',
                'data' => $report
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'File upload failed'
        ], 400);
    }

    /**
     * Get recent reports uploaded by the authenticated patient
     */
    public function recent(Request $request)
    {
        $patient = $request->user();

        $report = PatientReport::where('patient_id', $patient->patient_id)
            ->orderBy('patient_report_id', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Recent report fetched successfully',
            'data' => $report
        ], 200);
    }

    /**
     * Analyze patient report using Sensify Care AI Agent
     */
    public function analyze(Request $request)
    {
        $patient = $request->user();

        $validator = Validator::make($request->all(), [
            'type' => 'nullable|string|in:file,text',
            'patient_report_id' => 'nullable|exists:patient_report,patient_report_id',
            'file_url' => 'nullable|string',
            'question' => 'nullable|string',
            'text' => 'nullable|string',
            'language' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $finalType = $request->type ?? (($request->filled('patient_report_id') || $request->filled('file_url')) ? 'file' : 'text');
        $inputText = $request->text ?? $request->question;
        $language = $request->input('language', $patient->preferred_language ?: 'en');

        if ($finalType === 'file') {
            $fileUrl = $request->input('file_url');

            if (!$fileUrl) {
                $reportId = $request->input('patient_report_id');

                if ($reportId) {
                    $report = PatientReport::where('patient_id', $patient->patient_id)
                        ->where('patient_report_id', $reportId)
                        ->first();
                } else {
                    $report = PatientReport::where('patient_id', $patient->patient_id)
                        ->orderBy('patient_report_id', 'desc')
                        ->first();
                }

                if (!$report) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Report not found'
                    ], 404);
                }

                $fileUrl = $report->file_path;
            }

            $payload = [
                'type' => 'file',
                'file_url' => $fileUrl,
                'language' => $language,
            ];

            if ($inputText) {
                $payload['question'] = $inputText;
            }
        } else {
            // type is text
            if (!$inputText) {
                return response()->json([
                    'success' => false,
                    'message' => 'Question or text is required for text analysis'
                ], 422);
            }

            $payload = [
                'type' => 'text',
                'question' => $inputText,
                'language' => $language,
            ];
        }

        try {
            $response = Http::timeout(60)->post(
                'http://api.sensifycare.com/nodeapp/analyze-report',
                $payload
            );

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Report analyzed successfully',
                    'data' => $response->json()
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to analyze report',
                    'error' => $response->body()
                ], $response->status() ?: 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during analysis',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
