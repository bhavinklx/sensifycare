<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PatientAuthController extends Controller
{
    /**
     * Patient Login via Mobile Number
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_phone' => 'required|string',
            'patient_password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $patient = Patient::where('patient_phone', $request->patient_phone)->first();

        if (!$patient || !Hash::check($request->patient_password, $patient->patient_password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid mobile number or password.',
            ], 401);
        }

        // Generate token
        $token = $patient->createToken('patientToken')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'patient' => $patient,
                'token' => $token,
            ]
        ], 200);
    }

    /**
     * Patient Profile (Protected)
     */
    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'Patient profile retrieved',
            'data' => $request->user()
        ], 200);
    }

    /**
     * Patient Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout successful'
        ], 200);
    }
}
