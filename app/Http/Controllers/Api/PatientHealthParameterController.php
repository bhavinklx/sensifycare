<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthParameter;
use App\Models\Patient;
use App\Models\PatientHealthParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PatientHealthParameterController extends Controller
{
    /**
     * Get active health parameters with their options
     */
    public function index(Request $request)
    {
        $healthParameters = HealthParameter::active()
            ->orderBy('health_parameter_order')
            ->get()
            ->map(function ($parameter) {
                return [
                    'health_parameter_id' => $parameter->health_parameter_id,
                    'health_parameter_name' => $parameter->health_parameter_name,
                    'health_parameter_question' => $parameter->health_parameter_question,
                    'health_parameter_show_type' => $parameter->health_parameter_show_type,
                    'health_parameter_options' => $parameter->options_array,
                    'health_parameter_order' => $parameter->health_parameter_order,
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Health parameters retrieved successfully',
            'data' => $healthParameters
        ], 200);
    }

    /**
     * Save all patient health parameter answers in one request
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
            'answers.*.health_parameter_id' => 'required|integer|exists:health_parameters,health_parameter_id',
            'answers.*.answer' => 'required|string',
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
        $answers = $request->input('answers', []);

        DB::beginTransaction();
        try {
            // Remove old answers for this patient
            PatientHealthParameter::where('patient_id', $patient->patient_id)->delete();

            $insertData = [];
            foreach ($answers as $answer) {
                $insertData[] = [
                    'patient_id' => $patient->patient_id,
                    'health_parameter_id' => $answer['health_parameter_id'],
                    'health_parameter_answer' => $answer['answer'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($insertData)) {
                PatientHealthParameter::insert($insertData);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Health parameters saved successfully',
                'data' => [
                    'answers' => $answers
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to save health parameters',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get patient's saved health parameter answers
     */
    public function selected(Request $request)
    {
        /** @var Patient $patient */
        $patient = $request->user();
        $patient->load('healthParameters.healthParameter');

        $answers = $patient->healthParameters->map(function ($item) {
            return [
                'health_parameter_id' => $item->health_parameter_id,
                'health_parameter_name' => $item->healthParameter->health_parameter_name ?? null,
                'health_parameter_answer' => $item->health_parameter_answer,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Saved health parameters retrieved successfully',
            'data' => $answers
        ], 200);
    }
}
