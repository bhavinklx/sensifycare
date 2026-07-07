<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\UserDevice;
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

    /**
     * Send OTP to Patient Mobile Number
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $patient = Patient::where('patient_phone', $request->patient_phone)->first();

        if (!$patient) {
            // Create user as per previous request
            $patient = new Patient();
            $patient->patient_uid = uniqid('PT_');
            $patient->patient_phone = $request->patient_phone;
            $patient->patient_fname = 'Guest';
            $patient->patient_lname = 'User';
            $patient->patient_email = $request->patient_phone . '@example.com';
            $patient->patient_age = 0;
            $patient->patient_gender = 'other';
            $patient->patient_blood_group = 'Unknown';
            $patient->patient_password = Hash::make(uniqid());
            $patient->save();
        }

        // Generate a 6-digit OTP (for production, integrate an SMS gateway here)
        $otp = rand(100000, 999999);
        
        $patient->patient_otp = $otp;
        $patient->save();

        return response()->json([
            'status' => true,
            'message' => 'OTP sent successfully',
            'data' => [
                'patient_phone' => $patient->patient_phone,
                // In production, do not return the OTP in the response
                'otp' => $otp
            ]
        ], 200);
    }

    /**
     * Signup API
     */
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:patient,patient_email',
            'mobile' => 'required|string|unique:patient,patient_phone',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female,other'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $nameParts = explode(' ', $request->name, 2);
        $fname = $nameParts[0];
        $lname = isset($nameParts[1]) ? $nameParts[1] : '';
        $age = \Carbon\Carbon::parse($request->dob)->age;

        $patient = new Patient();
        $patient->patient_uid = uniqid('PT_');
        $patient->patient_fname = $fname;
        $patient->patient_lname = $lname;
        $patient->patient_email = $request->email;
        $patient->patient_phone = $request->mobile;
        $patient->patient_dob = $request->dob;
        $patient->patient_age = $age;
        $patient->patient_gender = $request->gender;
        $patient->patient_blood_group = 'Unknown'; // Default or nullable in schema? It's required in schema, so setting Unknown.
        $patient->patient_password = Hash::make(uniqid());
        
        // Generate OTP
        $otp = rand(100000, 999999);
        $patient->patient_otp = $otp;
        $patient->save();

        return response()->json([
            'status' => true,
            'message' => 'Signup successful. OTP sent.',
            'data' => [
                'patient_phone' => $patient->patient_phone,
                'otp' => $otp
            ]
        ], 201);
    }

    /**
     * Verify OTP and Login
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_phone' => 'required|string',
            'otp' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $patient = Patient::where('patient_phone', $request->patient_phone)->first();

        if (!$patient || $patient->patient_otp != $request->otp) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid mobile number or OTP.',
            ], 401);
        }

        // Clear OTP after successful verification
        $patient->patient_otp = null;
        $patient->save();

        // Save device details
        if ($request->has('push_notification_id')) {
            UserDevice::updateOrCreate(
                [
                    'user_id' => $patient->patient_id,
                    'user_type' => 'patient',
                    'push_notification_id' => $request->push_notification_id,
                ],
                [
                    'app_version' => $request->app_version,
                    'os_version' => $request->os_version,
                    'device_name' => $request->device_name,
                    'device_type' => $request->device_type,
                ]
            );
        }

        // Generate token
        $token = $patient->createToken('patientToken')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully. Login successful.',
            'data' => [
                'patient' => $patient,
                'token' => $token,
            ]
        ], 200);
    }
}
