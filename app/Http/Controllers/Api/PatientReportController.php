<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PatientReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

            // Create patient report record
            $report = PatientReport::create([
                'patient_id' => $patient->patient_id,
                'file_name' => $originalName,
                'file_path' => $filename,
                'file_size' => $fileSize,
                'status' => 'Processed',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Report uploaded successfully',
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
