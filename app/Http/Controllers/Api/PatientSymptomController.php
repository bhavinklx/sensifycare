<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Symptom;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientSymptomController extends Controller
{
    /**
     * Get active symptoms list
     */
    public function index(Request $request)
    {
        $symptoms = Symptom::where('symptom_status', '1')
            ->orderBy('symptom_order')
            ->get()
            ->map(function ($symptom) {
                return [
                    'symptom_id' => $symptom->symptom_id,
                    'symptom_name' => $symptom->symptom_name,
                    'symptom_desc' => $symptom->symptom_desc,
                    'symptom_image' => $symptom->symptom_image ? asset('uploads/symptom/' . $symptom->symptom_image) : null,
                    'symptom_order' => $symptom->symptom_order,
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Symptoms retrieved successfully',
            'data' => $symptoms
        ], 200);
    }

    /**
     * Save patient symptoms and optional custom description
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'symptom_ids' => 'nullable|array',
            'symptom_ids.*' => 'integer|exists:symptom,symptom_id',
            'patient_other_symptoms' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        /** @var Patient $patient */
        $patient = $request->user();

        // Sync symptoms
        $symptomIds = $request->input('symptom_ids', []);
        $patient->symptoms()->sync($symptomIds);

        // Save custom typed symptoms if any
        $patient->update([
            'patient_other_symptoms' => $request->input('patient_other_symptoms')
        ]);

        // Reload relationships
        $patient->load('symptoms');

        return response()->json([
            'status' => true,
            'message' => 'Symptoms saved successfully',
            'data' => [
                'patient' => $patient,
                'symptoms' => $patient->symptoms->map(function ($s) {
                    return [
                        'symptom_id' => $s->symptom_id,
                        'symptom_name' => $s->symptom_name,
                        'symptom_desc' => $s->symptom_desc,
                        'symptom_image' => $s->symptom_image ? asset('uploads/symptom/' . $s->symptom_image) : null,
                    ];
                })
            ]
        ], 200);
    }

    /**
     * Get patient's selected symptoms
     */
    public function selected(Request $request)
    {
        /** @var Patient $patient */
        $patient = $request->user();
        $patient->load('symptoms');

        return response()->json([
            'status' => true,
            'message' => 'Selected symptoms retrieved successfully',
            'data' => [
                'patient_other_symptoms' => $patient->patient_other_symptoms,
                'symptoms' => $patient->symptoms->map(function ($s) {
                    return [
                        'symptom_id' => $s->symptom_id,
                        'symptom_name' => $s->symptom_name,
                        'symptom_desc' => $s->symptom_desc,
                        'symptom_image' => $s->symptom_image ? asset('uploads/symptom/' . $s->symptom_image) : null,
                    ];
                })
            ]
        ], 200);
    }

    /**
     * Add new symptoms to patient's list (does not delete existing selections)
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'symptom_ids' => 'required|array',
            'symptom_ids.*' => 'integer|exists:symptom,symptom_id',
            'patient_other_symptoms' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        /** @var Patient $patient */
        $patient = $request->user();

        // Attach symptoms using syncWithoutDetaching
        $symptomIds = $request->input('symptom_ids', []);
        $patient->symptoms()->syncWithoutDetaching($symptomIds);

        // Update custom description if provided
        if ($request->has('patient_other_symptoms')) {
            $patient->update([
                'patient_other_symptoms' => $request->input('patient_other_symptoms')
            ]);
        }

        $patient->load('symptoms');

        return response()->json([
            'status' => true,
            'message' => 'Symptoms added successfully',
            'data' => [
                'patient' => $patient,
                'symptoms' => $patient->symptoms->map(function ($s) {
                    return [
                        'symptom_id' => $s->symptom_id,
                        'symptom_name' => $s->symptom_name,
                        'symptom_desc' => $s->symptom_desc,
                        'symptom_image' => $s->symptom_image ? asset('uploads/symptom/' . $s->symptom_image) : null,
                    ];
                })
            ]
        ], 200);
    }
}
