<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\UserDevice;
use App\Models\PatientHealthParameter;
use App\Models\HealthParameter;
use App\Models\Symptom;
use App\Models\PatientReport;
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

        // Send OTP via SMS gateway
        $this->sendSMS($patient->patient_phone, $otp . " is your one-time verification code for Sensify Care. OMNETS");

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
            'patient_name' => 'required|string',
            'email' => 'required|email|unique:patient,patient_email',
            'patient_phone' => 'required|string|unique:patient,patient_phone',
            'date_of_birth' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $nameParts = explode(' ', $request->patient_name, 2);
        $fname = $nameParts[0];
        $lname = isset($nameParts[1]) ? $nameParts[1] : '';
        $age = \Carbon\Carbon::parse($request->date_of_birth)->age;

        $patient = new Patient();
        $patient->patient_uid = uniqid('PT_');
        $patient->patient_fname = $fname;
        $patient->patient_lname = $lname;
        $patient->patient_email = $request->email;
        $patient->patient_phone = $request->patient_phone;
        $patient->patient_dob = $request->date_of_birth;
        $patient->patient_age = $age;
        $patient->patient_gender = 'other'; // default
        $patient->patient_blood_group = 'Unknown';
        $patient->patient_password = Hash::make(uniqid());

        // Generate OTP
        $otp = rand(100000, 999999);
        $patient->patient_otp = $otp;
        $patient->save();

        // Send OTP via SMS gateway
        $this->sendSMS($patient->patient_phone, $otp . " is your one-time verification code for Sensify Care. OMNETS");

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

        return response()->json([
            'success' => true,
            'message' => 'Profile fetched successfully',
            'data' => $this->formatPatientProfileResponse($patient)
        ], 200);
    }

    /**
     * Update Patient Profile
     */
    public function updateProfile(Request $request)
    {
        /** @var Patient $patient */
        $patient = $request->user();
        $step = $request->input('profile_step', 'basic');

        $rules = [
            'profile_step' => 'required|string|in:basic,health,symptoms,edit',
        ];

        if ($step === 'basic') {
            $rules = array_merge($rules, [
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'dob' => 'required|date',
                'gender' => 'required|string|max:20',
                'height_cm' => 'required|integer',
                'weight_kg' => 'required|integer',
                'city' => 'required|string|max:100',
                'preferred_language' => 'required|string|max:100',
                'patient_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } elseif ($step === 'health') {
            $dynamicRules = [];
            $allParams = HealthParameter::active()->get();
            foreach ($allParams as $param) {
                $key = $this->getHealthParameterKey($param->health_parameter_name);
                $paramId = $param->health_parameter_id;
                $dynamicRules[$key] = 'nullable|string';
                $dynamicRules[$paramId] = 'nullable|string';
            }
            $rules = array_merge($rules, $dynamicRules);
        } elseif ($step === 'symptoms') {
            $rules = array_merge($rules, [
                'symptoms' => 'nullable', // Array or JSON string representation
                'other_symptoms' => 'nullable|string',
            ]);
        } elseif ($step === 'edit') {
            $editRules = [
                'first_name' => 'nullable|string|max:100',
                'last_name' => 'nullable|string|max:100',
                'email' => 'nullable|email|unique:patient,patient_email,' . $patient->patient_id . ',patient_id',
                'patient_phone' => 'nullable|string|unique:patient,patient_phone,' . $patient->patient_id . ',patient_id',
                'dob' => 'nullable|date',
                'city' => 'nullable|string|max:100',
                'height_cm' => 'nullable|integer',
                'weight_kg' => 'nullable|integer',
                'patient_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'symptoms' => 'nullable',
                'other_symptoms' => 'nullable|string',
            ];

            $allParams = HealthParameter::active()->get();
            foreach ($allParams as $param) {
                $key = $this->getHealthParameterKey($param->health_parameter_name);
                $paramId = $param->health_parameter_id;
                $editRules[$key] = 'nullable|string';
                $editRules[$paramId] = 'nullable|string';
            }
            $rules = array_merge($rules, $editRules);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle Image Upload
        if ($request->hasFile('patient_image')) {
            if ($patient->patient_image && file_exists(public_path('uploads/patient/' . $patient->patient_image))) {
                @unlink(public_path('uploads/patient/' . $patient->patient_image));
            }
            $image = $request->file('patient_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/patient'), $imageName);
            $patient->patient_image = $imageName;
        }

        // Apply profile_step
        $patient->profile_step = $step;

        // Process step data
        if ($step === 'basic') {
            $patient->patient_fname = $request->first_name;
            $patient->patient_lname = $request->last_name;
            $patient->patient_dob = $request->dob;
            $patient->patient_age = \Carbon\Carbon::parse($request->dob)->age;
            $patient->patient_gender = strtolower($request->gender);
            $patient->height_cm = $request->height_cm;
            $patient->weight_kg = $request->weight_kg;
            $patient->patient_city = $request->city;
            $patient->preferred_language = $request->preferred_language;
            $patient->save();
        } elseif ($step === 'health') {
            $allParams = HealthParameter::active()->get();
            foreach ($allParams as $param) {
                $key = $this->getHealthParameterKey($param->health_parameter_name);
                $paramId = $param->health_parameter_id;

                $answer = null;
                if ($request->has($key)) {
                    $answer = $request->input($key);
                } elseif ($request->has($paramId)) {
                    $answer = $request->input($paramId);
                }

                if ($answer !== null) {
                    PatientHealthParameter::updateOrCreate(
                        [
                            'patient_id' => $patient->patient_id,
                            'health_parameter_id' => $paramId,
                        ],
                        [
                            'health_parameter_answer' => $answer,
                        ]
                    );
                }
            }
            $patient->save();
        } elseif ($step === 'symptoms') {
            $symptomsInput = $request->input('symptoms', '[]');
            if (is_string($symptomsInput)) {
                $symptomsInput = json_decode($symptomsInput, true) ?: [];
            }

            $activeSymptoms = Symptom::where('symptom_status', '1')->get();
            $slugToIdMap = [];
            foreach ($activeSymptoms as $symptom) {
                $slugToIdMap[$this->getSymptomKey($symptom->symptom_name)] = $symptom->symptom_id;
            }

            $integerIds = [];
            foreach ($symptomsInput as $inputSymptom) {
                if (is_numeric($inputSymptom)) {
                    $integerIds[] = (int) $inputSymptom;
                } elseif (isset($slugToIdMap[$inputSymptom])) {
                    $integerIds[] = $slugToIdMap[$inputSymptom];
                }
            }

            $patient->other_symptoms = $request->input('other_symptoms');
            $patient->is_profile_complete = true;
            $patient->save();

            $patient->symptoms()->sync($integerIds);
        } elseif ($step === 'edit') {
            if ($request->has('first_name'))
                $patient->patient_fname = $request->first_name;
            if ($request->has('last_name'))
                $patient->patient_lname = $request->last_name;
            if ($request->has('email'))
                $patient->patient_email = $request->email;
            if ($request->has('patient_phone'))
                $patient->patient_phone = $request->patient_phone;
            if ($request->has('dob')) {
                $patient->patient_dob = $request->dob;
                $patient->patient_age = \Carbon\Carbon::parse($request->dob)->age;
            }
            if ($request->has('city'))
                $patient->patient_city = $request->city;
            if ($request->has('height_cm'))
                $patient->height_cm = $request->height_cm;
            if ($request->has('weight_kg'))
                $patient->weight_kg = $request->weight_kg;
            $patient->save();

            // Symptoms if present in edit request
            if ($request->has('symptoms')) {
                $symptomsInput = $request->input('symptoms', '[]');
                if (is_string($symptomsInput)) {
                    $symptomsInput = json_decode($symptomsInput, true) ?: [];
                }

                $activeSymptoms = Symptom::where('symptom_status', '1')->get();
                $slugToIdMap = [];
                foreach ($activeSymptoms as $symptom) {
                    $slugToIdMap[$this->getSymptomKey($symptom->symptom_name)] = $symptom->symptom_id;
                }

                $integerIds = [];
                foreach ($symptomsInput as $inputSymptom) {
                    if (is_numeric($inputSymptom)) {
                        $integerIds[] = (int) $inputSymptom;
                    } elseif (isset($slugToIdMap[$inputSymptom])) {
                        $integerIds[] = $slugToIdMap[$inputSymptom];
                    }
                }

                $patient->symptoms()->sync($integerIds);
            }
            if ($request->has('other_symptoms')) {
                $patient->other_symptoms = $request->input('other_symptoms');
                $patient->save();
            }

            // Health parameters if present in edit request
            $allParams = HealthParameter::active()->get();
            foreach ($allParams as $param) {
                $key = $this->getHealthParameterKey($param->health_parameter_name);
                $paramId = $param->health_parameter_id;

                $answer = null;
                if ($request->has($key)) {
                    $answer = $request->input($key);
                } elseif ($request->has($paramId)) {
                    $answer = $request->input($paramId);
                }

                if ($answer !== null) {
                    PatientHealthParameter::updateOrCreate(
                        [
                            'patient_id' => $patient->patient_id,
                            'health_parameter_id' => $paramId,
                        ],
                        [
                            'health_parameter_answer' => $answer,
                        ]
                    );
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $this->formatPatientProfileResponse($patient)
        ], 200);
    }

    /**
     * Delete Patient Account (Soft Delete)
     */
    public function deleteAccount(Request $request)
    {
        $patient = $request->user();

        // Revoke all tokens
        $patient->tokens()->delete();

        // Soft delete the patient
        $patient->delete();

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully.'
        ], 200);
    }

    /**
     * Get unified dashboard preventive health metrics.
     */
    public function getDashboard(Request $request)
    {
        $patient = $request->user();

        // 1. Lab Report Analysis Pillar
        $latestReport = PatientReport::where('patient_id', $patient->patient_id)
            ->orderBy('created_at', 'desc')
            ->first();

        $labReportScore = 100;
        $labReportSubtitle = "No reports analyzed. Upload a report to begin.";
        if ($latestReport) {
            $totalMarkers = (int)$latestReport->markers_count;
            $abnormalMarkers = (int)$latestReport->abnormal_count;
            
            if ($totalMarkers > 0) {
                $okMarkers = (int)$latestReport->ok_count;
                $labReportScore = (int)round(($okMarkers / $totalMarkers) * 100);
                $labReportScore = max(10, min(100, $labReportScore));
            } else {
                $labReportScore = 100;
            }

            $totalReportsCount = PatientReport::where('patient_id', $patient->patient_id)->count();
            if ($abnormalMarkers > 0) {
                $labReportSubtitle = "$totalReportsCount " . ($totalReportsCount === 1 ? "report" : "reports") . " analyzed, $abnormalMarkers abnormal " . ($abnormalMarkers === 1 ? "marker" : "markers") . " detected";
            } else {
                $labReportSubtitle = "$totalReportsCount " . ($totalReportsCount === 1 ? "report" : "reports") . " analyzed, most markers normal";
            }
        }

        // 2. Lifestyle & Habits Pillar
        $activeParams = HealthParameter::active()->orderBy('health_parameter_order')->get();
        $healthAnswers = PatientHealthParameter::where('patient_id', $patient->patient_id)
            ->get()
            ->keyBy('health_parameter_id');

        $lifestylePoints = 0;
        $needsImprovement = [];
        foreach ($activeParams as $param) {
            $key = $this->getHealthParameterKey($param->health_parameter_name);
            $selectedAnswer = isset($healthAnswers[$param->health_parameter_id]) 
                ? $healthAnswers[$param->health_parameter_id]->health_parameter_answer 
                : null;

            $isHealthy = false;
            if ($selectedAnswer !== null) {
                switch ($key) {
                    case 'average_sleep':
                        if (in_array($selectedAnswer, ['7 - 8 hours', 'More than 8 hours'])) $isHealthy = true;
                        break;
                    case 'physical_activity':
                        if (in_array($selectedAnswer, ['3 - 4 days / week', '5 - 6 days / week', 'Daily'])) $isHealthy = true;
                        break;
                    case 'smoking':
                        if ($selectedAnswer === 'Never') $isHealthy = true;
                        break;
                    case 'alcohol':
                        if (in_array($selectedAnswer, ['Never', 'Occasionally'])) $isHealthy = true;
                        break;
                    case 'water_intake':
                        if (in_array($selectedAnswer, ['2 - 3 L', 'More than 3 L'])) $isHealthy = true;
                        break;
                    case 'diet_type':
                        if (in_array($selectedAnswer, ['Vegetarian', 'Vegan', 'Eggetarian', 'Mixed'])) $isHealthy = true;
                        break;
                    case 'stress_level':
                        if (in_array($selectedAnswer, ['Low', 'Moderate'])) $isHealthy = true;
                        break;
                    case 'menstrual_cycle':
                        if (in_array($selectedAnswer, ['Regular', 'Not Applicable'])) $isHealthy = true;
                        break;
                    case 'fasting':
                    case 'work_type':
                        // Fasting and Work Type answers are considered fine as long as they are completed
                        $isHealthy = true;
                        break;
                }
            }

            if ($isHealthy) {
                $lifestylePoints += 10;
            } else {
                // If not healthy or unanswered, mark for improvement
                $needsImprovement[] = str_replace('Average ', '', str_replace(' Level', '', $param->health_parameter_name));
            }
        }
        $lifestyleScore = $lifestylePoints;
        
        $lifestyleSubtitle = "Great lifestyle habits!";
        if ($lifestyleScore < 100 && !empty($needsImprovement)) {
            // Pick top 2 improvements to display in subtitle
            $topImprovements = array_slice($needsImprovement, 0, 2);
            $lifestyleSubtitle = implode(' & ', $topImprovements) . " need improvement";
        }

        // 3. Symptom Profile Pillar
        $symptomsCount = $patient->symptoms()->count();
        $hasOtherSymptoms = !empty($patient->other_symptoms);

        if ($symptomsCount === 0 && !$hasOtherSymptoms) {
            $symptomScore = 100;
            $symptomSubtitle = "No symptoms reported, looking great!";
        } else {
            $symptomScore = 0;
            $symptomSubtitle = "$symptomsCount " . ($symptomsCount === 1 ? "symptom" : "symptoms") . " reported, monitoring needed";
        }

        // 4. Profile Completeness Pillar
        $profileFields = [
            'patient_fname' => 15,
            'patient_lname' => 15,
            'patient_dob' => 15,
            'patient_gender' => 10,
            'height_cm' => 15,
            'weight_kg' => 15,
            'patient_city' => 10,
            'patient_image' => 5,
        ];

        $completenessScore = 0;
        $missingFields = [];
        foreach ($profileFields as $field => $pct) {
            if (!empty($patient->$field)) {
                $completenessScore += $pct;
            } else {
                $missingFields[] = $field;
            }
        }

        $profileSubtitle = "Profile fully complete";
        if ($completenessScore < 100 && !empty($missingFields)) {
            // Suggest adding a missing field
            $firstMissing = $missingFields[0];
            $friendlyName = str_replace(['patient_', '_cm', '_kg'], ['', '', ''], $firstMissing);
            $friendlyName = str_replace('_', ' ', $friendlyName);
            $profileSubtitle = "Almost complete — add $friendlyName";
        }

        // Calculate Total Health Score (Average of 4 pillars)
        $totalHealthScore = (int)round(($labReportScore + $lifestyleScore + $symptomScore + $completenessScore) / 4);

        // Map health score to quality label
        if ($totalHealthScore >= 80) {
            $label = "Good";
            $text = "You're on the right track! A few improvements can boost your score.";
        } elseif ($totalHealthScore >= 50) {
            $label = "Fair";
            $text = "Your health parameters are average. Making lifestyle changes can help improve them.";
        } else {
            $label = "Poor";
            $text = "Your score indicates attention is needed. We recommend consulting a healthcare provider.";
        }

        // Calculate Key Insights
        $totalReportsCount = PatientReport::where('patient_id', $patient->patient_id)->count();
        $totalAbnormal = (int)PatientReport::where('patient_id', $patient->patient_id)->sum('abnormal_count');
        $healthyHabitsCount = (int)($lifestyleScore / 10);

        return response()->json([
            'success' => true,
            'message' => 'Dashboard fetched successfully',
            'data' => [
                'health_score' => [
                    'score' => $totalHealthScore,
                    'label' => $label,
                    'text' => $text,
                    'breakdown' => [
                        'lab_report' => [
                            'title' => 'Lab Report Analysis',
                            'score' => $labReportScore,
                            'subtitle' => $labReportSubtitle
                        ],
                        'lifestyle' => [
                            'title' => 'Lifestyle & Habits',
                            'score' => $lifestyleScore,
                            'subtitle' => $lifestyleSubtitle
                        ],
                        'symptoms' => [
                            'title' => 'Symptom Profile',
                            'score' => $symptomScore,
                            'subtitle' => $symptomSubtitle
                        ],
                        'profile' => [
                            'title' => 'Profile Completeness',
                            'score' => $completenessScore,
                            'subtitle' => $profileSubtitle
                        ]
                    ]
                ],
                'key_insights' => [
                    'reports_count' => $totalReportsCount,
                    'risk_status_count' => $totalAbnormal,
                    'healthy_habits_score' => "{$healthyHabitsCount}/10"
                ]
            ]
        ], 200);
    }

    /**
     * Helper to map health parameter DB names to JSON keys
     */
    private function getHealthParameterKey($name)
    {
        $slug = strtolower(str_replace([' ', ','], ['_', ''], $name));
        if ($slug === 'diet') {
            return 'diet_type';
        }
        return $slug;
    }

    /**
     * Helper to map symptom DB names to JSON keys
     */
    private function getSymptomKey($name)
    {
        $processed = str_replace(' or ', ' ', $name);
        return strtolower(str_replace([' ', ','], ['_', ''], $processed));
    }

    private function formatPatientProfileResponse(Patient $patient)
    {
        $allHealthParameters = HealthParameter::active()->orderBy('health_parameter_order')->get();
        $healthAnswers = PatientHealthParameter::where('patient_id', $patient->patient_id)
            ->get()
            ->keyBy('health_parameter_id');

        $responseFields = [];
        $healthOptions = [];

        foreach ($allHealthParameters as $param) {
            $key = $this->getHealthParameterKey($param->health_parameter_name);
            $emoji = $param->health_parameter_emoji ?: '❓';

            $selectedAnswer = isset($healthAnswers[$param->health_parameter_id])
                ? $healthAnswers[$param->health_parameter_id]->health_parameter_answer
                : null;

            $responseFields[$key] = $selectedAnswer;

            $healthOptions[] = [
                'id' => $param->health_parameter_id,
                'title' => $param->health_parameter_name,
                'emoji' => $emoji,
                'options' => $param->options_array,
                'selected' => $selectedAnswer,
            ];
        }

        $activeSymptoms = Symptom::where('symptom_status', '1')
            ->orderBy('symptom_order')
            ->get();

        $symptomConfig = $activeSymptoms->map(function ($symptom) {
            return [
                'id' => $this->getSymptomKey($symptom->symptom_name),
                'title' => $symptom->symptom_name,
                'description' => $symptom->symptom_desc,
                'emoji' => $symptom->symptom_emoji ?: '❓',
            ];
        })->toArray();

        $profileData = [
            'patient_id' => $patient->patient_id,
            'first_name' => $patient->patient_fname,
            'last_name' => $patient->patient_lname,
            'patient_name' => trim($patient->patient_fname . ' ' . $patient->patient_lname),
            'email' => $patient->patient_email,
            'patient_phone' => $patient->patient_phone,
            'dob' => $patient->patient_dob,
            'age' => $patient->patient_age,
            'gender' => ucfirst($patient->patient_gender),
            'height_cm' => $patient->height_cm,
            'weight_kg' => $patient->weight_kg,
            'city' => $patient->patient_city,
            'preferred_language' => $patient->preferred_language,
        ];

        // Merge health parameters top-level fields dynamically
        $profileData = array_merge($profileData, $responseFields);

        $patientSymptomsSlugs = $patient->symptoms()->get()->map(function ($symptom) {
            return $this->getSymptomKey($symptom->symptom_name);
        })->toArray();

        $profileData = array_merge($profileData, [
            'health_options' => $healthOptions,
            'symptoms' => $patientSymptomsSlugs,
            'other_symptoms' => $patient->other_symptoms ?: '',
            'symptom_options' => $symptomConfig,

            'patient_image' => $patient->patient_image ? asset('uploads/patient/' . $patient->patient_image) : '',
            'profile_step' => $patient->profile_step ?: 'basic',
            'is_profile_complete' => (bool) $patient->is_profile_complete,
        ]);

        return $profileData;
    }

    /**
     * Send SMS via Omnet Solution gateway
     */
    private function sendSMS($mobileNumber, $message)
    {
        $authKey = "23a1a991572963a7d9a64c436a3dfd";
        $senderId = "OMNETS";
        $message = urlencode($message);

        $url = "http://sms1.omnetsolution.com/rest/services/sendSMS/sendGroupSms?AUTH_KEY=$authKey&message=$message&senderId=$senderId&routeId=1&mobileNos=$mobileNumber&smsContentType=english";
        $data = @file_get_contents($url);

        return true;
    }
}
