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
                    $report->update([
                        'status' => 'Processed',
                        'ocr_data' => $response->json(),
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
}
