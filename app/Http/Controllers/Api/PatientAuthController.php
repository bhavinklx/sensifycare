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
            return response()->json([
                'status' => false,
                'message' => 'Mobile number not found.',
            ], 404);
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
            'dob' => 'required|date'
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
        //$patient->patient_gender = $request->gender;
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

    /**
     * Get Authenticated Patient Profile
     */
    public function getProfile(Request $request)
    {
        $patient = $request->user();
        $patientData = $patient->toArray();
        $patientData['patient_image_url'] = $patient->patient_image ? asset('uploads/patient/' . $patient->patient_image) : null;

        return response()->json([
            'status' => true,
            'message' => 'Profile retrieved successfully',
            'data' => $patientData
        ], 200);
    }

    /**
     * Update Patient Profile
     */
    public function updateProfile(Request $request)
    {
        /** @var Patient $patient */
        $patient = $request->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'nullable|email|unique:patient,patient_email,' . $patient->patient_id . ',patient_id',
            'phone' => 'nullable|string|unique:patient,patient_phone,' . $patient->patient_id . ',patient_id',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string|max:20',
            'marital_status' => 'nullable|string|max:30',
            'occupation' => 'nullable|string|max:100',
            'blood_group' => 'nullable|string|max:10',
            'blood_pressure' => 'nullable|string|max:20',
            'sugar_level' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'patient_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('patient_image')) {
            // Delete old image if exists
            if ($patient->patient_image && file_exists(public_path('uploads/patient/' . $patient->patient_image))) {
                @unlink(public_path('uploads/patient/' . $patient->patient_image));
            }
            
            $image = $request->file('patient_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/patient'), $imageName);
            $patient->patient_image = $imageName;
        }

        if ($request->has('first_name')) {
            $patient->patient_fname = $request->first_name;
        }
        if ($request->has('last_name')) {
            $patient->patient_lname = $request->last_name;
        }
        if ($request->has('email')) {
            $patient->patient_email = $request->email;
        }
        if ($request->has('phone')) {
            $patient->patient_phone = $request->phone;
        }
        if ($request->has('dob')) {
            $patient->patient_dob = $request->dob;
            $patient->patient_age = \Carbon\Carbon::parse($request->dob)->age;
        }
        if ($request->has('gender')) {
            $patient->patient_gender = $request->gender;
        }
        if ($request->has('marital_status')) {
            $patient->patient_marital_status = $request->marital_status;
        }
        if ($request->has('occupation')) {
            $patient->patient_occupation = $request->occupation;
        }
        if ($request->has('blood_group')) {
            $patient->patient_blood_group = $request->blood_group;
        }
        if ($request->has('blood_pressure')) {
            $patient->patient_blood_pressure = $request->blood_pressure;
        }
        if ($request->has('sugar_level')) {
            $patient->patient_sugar_level = $request->sugar_level;
        }
        if ($request->has('address')) {
            $patient->patient_address = $request->address;
        }
        if ($request->has('city')) {
            $patient->patient_city = $request->city;
        }
        if ($request->has('state')) {
            $patient->patient_state = $request->state;
        }
        if ($request->has('postal_code')) {
            $patient->patient_postal_code = $request->postal_code;
        }

        $patient->save();

        $patientData = $patient->toArray();
        $patientData['patient_image_url'] = $patient->patient_image ? asset('uploads/patient/' . $patient->patient_image) : null;

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => $patientData
        ], 200);
    }
}
