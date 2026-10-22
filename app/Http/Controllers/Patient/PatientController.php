<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\HealthParameter;
use App\Models\PatientHealthParameter;
use App\Models\PatientReport;
use App\Models\Symptom;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class PatientController extends Controller
{
    function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
    }
    
    public function create()
    {
        return view("admin.patient.create");
    }

    public function insert(Request $request)
    {
        $this->validateData($request);

        $patient = new Patient();
        $this->saveUpdateData($patient, $request);

        Session::flash('successMsg', 'Patient added successfully');
        return response()->json(['redirect_url' => route('patient-list')]);
    }
    
    public function edit($id)
    {
        $patientDetail = Patient::find($id);
        return view("admin.patient.edit", compact('patientDetail'));
    }

    public function update(Request $request)
    {
        $this->validateData($request);

        $patient = Patient::findOrFail($request->patient_id);
        $this->saveUpdateData($patient, $request, true);

        Session::flash('successMsg', 'Patient updated successfully');
        return response()->json(['redirect_url' => route('patient-list')]);
    }

    public function view()
    {
        return view("admin.patient.list");
    }

    public function detail($id)
    {
        $patient = Patient::with([
            'symptoms',
            'healthParameters.healthParameter',
            'reports',
            'notifications'
        ])->findOrFail($id);

        // 1. Lab Reports analysis
        $totalReportsCount = $patient->reports->count();
        $totalAbnormal = (int) $patient->reports->sum('abnormal_count');
        $totalOk = (int) $patient->reports->sum('ok_count');
        $totalMarkers = (int) $patient->reports->sum('markers_count');

        if ($totalMarkers > 0) {
            $labReportScore = (int) round(($totalOk / $totalMarkers) * 100);
        } else {
            $labReportScore = 100;
        }

        // 2. Lifestyle Habits analysis
        $activeParams = HealthParameter::active()->orderBy('health_parameter_order')->get();
        $healthAnswers = $patient->healthParameters->keyBy('health_parameter_id');

        $lifestylePoints = 0;
        $lifestyleDetails = [];
        foreach ($activeParams as $param) {
            $paramName = strtolower(trim($param->health_parameter_name));
            $userAnswer = isset($healthAnswers[$param->health_parameter_id])
                ? $healthAnswers[$param->health_parameter_id]->health_parameter_answer
                : null;

            $isHealthy = false;
            $statusLabel = 'Needs Attention';
            $badgeClass = 'bg-danger-subtle text-danger';

            if ($userAnswer !== null) {
                if (str_contains($paramName, 'sleep')) {
                    if (in_array($userAnswer, ['7 - 8 hours', 'More than 8 hours'])) {
                        $isHealthy = true;
                        $statusLabel = 'Optimal';
                        $badgeClass = 'bg-success-subtle text-success';
                    } elseif ($userAnswer == '6 - 7 hours') {
                        $isHealthy = false;
                        $statusLabel = 'Moderate';
                        $badgeClass = 'bg-warning-subtle text-warning';
                    }
                } elseif (str_contains($paramName, 'physical')) {
                    if (in_array($userAnswer, ['3 - 4 days / week', '5 - 6 days / week', 'Daily'])) {
                        $isHealthy = true;
                        $statusLabel = 'Active';
                        $badgeClass = 'bg-success-subtle text-success';
                    } elseif ($userAnswer == '1 - 2 days / week') {
                        $statusLabel = 'Moderate';
                        $badgeClass = 'bg-warning-subtle text-warning';
                    }
                } elseif (str_contains($paramName, 'smoking')) {
                    if ($userAnswer === 'Never') {
                        $isHealthy = true;
                        $statusLabel = 'Non-Smoker';
                        $badgeClass = 'bg-success-subtle text-success';
                    }
                } elseif (str_contains($paramName, 'alcohol')) {
                    if (in_array($userAnswer, ['Never', 'Occasionally'])) {
                        $isHealthy = true;
                        $statusLabel = $userAnswer === 'Never' ? 'Non-Drinker' : 'Moderate';
                        $badgeClass = 'bg-success-subtle text-success';
                    }
                } elseif (str_contains($paramName, 'water')) {
                    if (in_array($userAnswer, ['2 - 3 L', 'More than 3 L'])) {
                        $isHealthy = true;
                        $statusLabel = 'Well Hydrated';
                        $badgeClass = 'bg-success-subtle text-success';
                    } elseif ($userAnswer == '1.5 - 2 L') {
                        $statusLabel = 'Fair';
                        $badgeClass = 'bg-warning-subtle text-warning';
                    }
                } elseif (str_contains($paramName, 'diet')) {
                    if (in_array($userAnswer, ['Vegetarian', 'Vegan', 'Eggetarian', 'Mixed'])) {
                        $isHealthy = true;
                        $statusLabel = 'Healthy Diet';
                        $badgeClass = 'bg-success-subtle text-success';
                    }
                } elseif (str_contains($paramName, 'stress')) {
                    if (in_array($userAnswer, ['Low', 'Moderate'])) {
                        $isHealthy = true;
                        $statusLabel = $userAnswer . ' Stress';
                        $badgeClass = $userAnswer == 'Low' ? 'bg-success-subtle text-success' : 'bg-info-subtle text-info';
                    }
                } elseif (str_contains($paramName, 'menstrual')) {
                    if (in_array($userAnswer, ['Regular', 'Not Applicable'])) {
                        $isHealthy = true;
                        $statusLabel = $userAnswer;
                        $badgeClass = 'bg-secondary-subtle text-secondary';
                    }
                } else {
                    $isHealthy = true;
                    $statusLabel = 'Reported';
                    $badgeClass = 'bg-secondary-subtle text-secondary';
                }
            } else {
                $statusLabel = 'Not Answered';
                $badgeClass = 'bg-secondary-subtle text-secondary';
            }

            if ($isHealthy) {
                $lifestylePoints += 10;
            }

            $lifestyleDetails[] = [
                'name' => $param->health_parameter_name,
                'emoji' => $param->health_parameter_emoji ?? '🩺',
                'question' => $param->health_parameter_question,
                'answer' => $userAnswer ?? 'Not provided',
                'is_healthy' => $isHealthy,
                'status_label' => $statusLabel,
                'badge_class' => $badgeClass
            ];
        }
        $lifestyleScore = $lifestylePoints;

        // 3. Symptoms Pillar
        $symptomsCount = $patient->symptoms->count();
        $hasOtherSymptoms = !empty($patient->patient_other_symptoms);
        if ($symptomsCount === 0 && !$hasOtherSymptoms) {
            $symptomScore = 100;
        } else {
            $symptomScore = 0;
        }

        // 4. Completeness Pillar
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
        foreach ($profileFields as $field => $pct) {
            if (!empty($patient->$field)) {
                $completenessScore += $pct;
            }
        }

        // Health Score Average
        $totalHealthScore = (int) round(($labReportScore + $lifestyleScore + $symptomScore + $completenessScore) / 4);
        if ($totalHealthScore >= 80) {
            $scoreLabel = 'Good';
            $scoreBadge = 'bg-success-subtle text-success border border-success';
            $scoreColor = 'success';
        } elseif ($totalHealthScore >= 50) {
            $scoreLabel = 'Fair';
            $scoreBadge = 'bg-warning-subtle text-warning border border-warning';
            $scoreColor = 'warning';
        } else {
            $scoreLabel = 'Needs Attention';
            $scoreBadge = 'bg-danger-subtle text-danger border border-danger';
            $scoreColor = 'danger';
        }

        return view('admin.patient.view', compact(
            'patient',
            'totalReportsCount',
            'totalAbnormal',
            'totalOk',
            'totalMarkers',
            'labReportScore',
            'lifestyleScore',
            'symptomScore',
            'completenessScore',
            'totalHealthScore',
            'scoreLabel',
            'scoreBadge',
            'scoreColor',
            'lifestyleDetails'
        ));
    }

    public function load_table(Request $request)
    {
        $patientDetail = Patient::orderBy("patient_order");
        return DataTables::of($patientDetail)
            ->editColumn("checkbox", function ($patient){
                return '<div class="form-check m-0"> <input class="form-check-input check_class" type="checkbox" id="check[]" name="check[]" value="' . $patient->patient_id . '"> </div>';
            })
            ->editColumn("uid", function ($patient){
                return $patient->patient_uid;
            })
            ->editColumn("title", function ($patient){
                return '<a href="'.route('patient-view', ['id' => $patient->patient_id]).'" class="text-primary fw-medium text-decoration-none" data-bs-toggle="tooltip" title="View Patient Profile">'.e($patient->patient_fname . ' ' . $patient->patient_lname).'</a>';
            })
            ->editColumn("email", function ($patient){
                return $patient->patient_email;
            })
            ->editColumn("phone", function ($patient){
                return $patient->patient_phone;
            })
            ->editColumn("gender", function ($patient){
                if ($patient->patient_gender == 'male') {
                    return '<span class="badge bg-info-subtle text-info">'.ucwords($patient->patient_gender).'</span>';
                } else {
                    return '<span class="badge bg-warning-subtle text-warning">'.ucwords($patient->patient_gender).'</span>';
                }
            })
            ->editColumn("age", function ($patient){
                return $patient->patient_age;
            })
            ->editColumn("blood_group", function ($patient){
                return strtoupper($patient->patient_blood_group);
            })
            ->editColumn("date", function ($patient){
                return date('d-m-Y h:i:s A', strtotime($patient->created_at));
            })
            ->editColumn("status", function ($patient) {
                if ($patient->patient_status == '1') {
                    return '<div id="td_status_' . $patient->patient_id . '"><a href="javascript:void(0)" onclick="change_status(' . $patient->patient_id . ',0)" ><span class="badge bg-success">Active</span></a></div>';
                } else {
                    return '<div id="td_status_' . $patient->patient_id . '"><a href="javascript:void(0)" onclick="change_status(' . $patient->patient_id . ',1)" ><span class="badge bg-danger">Inactive</span></a></div>';
                }
            })
            ->editColumn("action", function ($patient){
                $action = '<div class="d-inline-flex gap-1">';
                $action.= '<a href="'.route("patient-view", ['id' => $patient->patient_id]).'" class="btn btn-outline-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View Patient Profile"> <i class="ri-eye-line"></i> </a>';
                if (auth()->user()->can('patient-delete')) {
                    $action.= '<button class="btn btn-outline-danger btn-sm" onclick="openDeleteModal(' . $patient->patient_id . ');" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete Patient"> <i class="ri-delete-bin-line"></i> </button>';
                }
                /*if (auth()->user()->can('patient-edit')) {
                    $action.= '<a href="'.route("patient-edit", ['id' => $patient->patient_id]).'" class="btn btn-outline-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Patient"> <i class="ri-edit-box-line"></i> </a>';
                }*/
                $action.= '</div>';
                return $action;
            })
            ->setRowClass(function () {
                return 'row1';
            })
            ->setRowAttr([
                "id" => function ($patient) {
                    return 'row_' . $patient->patient_id;
                },
                "data-id" => function ($patient) {
                    return $patient->patient_id;
                }
            ])
            ->rawColumns(["checkbox", "title", "gender", "image", "status", "action"])
            ->make(true);
    }

    public function change_status(Request $request)
    {
        if (!$request->ajax())
        {
            exit('No direct script access allowed');
        }
        if (!empty($request->all()))
        {
            Patient::where("patient_id", $request->patient_id)->update(["patient_status" => $request->status]);
            if ($request->status == 1) {
                echo 'Status Activate successfully';
            } else if ($request->status == 0){
                echo 'Status Inactivate successfully';
            }
        }
    }

    public function update_order(Request $request)
    {
        //print_r($request->order); exit();
        foreach ($request->order as $order) {
            Patient::where("patient_id", $order["patient_id"])->update(["patient_order" => $order["position"]]);
        }
        echo 'Patient order changed successfully.';
    }

    public function delete(Request $request)
    {
        $patient = Patient::findOrFail($request->patient_id);
        $this->deleteFile($patient->patient_image);

        $patient->delete();
        return response('Patient deleted successfully.');
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            "patient_fname"             => 'required|string|max:255',
            "patient_lname"             => 'required|string|max:255',
            "patient_email"             => 'required|string|max:255',
            "patient_phone"             => "required"
        ]);
    }

    private function saveUpdateData(Patient $patient, Request $request, $isUpdate = false)
    {
        if ($request->hasFile('patient_image')) {
            if ($isUpdate) {
                $this->deleteFile($patient->patient_image);
            }
            $patient->patient_image     = $this->uploadFile($request->file('patient_image'));
        }

        //Dropzone async upload
        if ($request->patient_image) {
            $patient->patient_image     = $request->patient_image; // filename string
        }
        
        if ($isUpdate) {
            $patient->updated_at        = date('Y-m-d H:i:s');
        } else {
            $lastOrder                  = Patient::orderBy("patient_order", "DESC")->first();
            $patient->patient_order     = (!empty($lastOrder)) ? $lastOrder->patient_order + 1 : 1;
            $patient->created_at        = date('Y-m-d H:i:s');
        }

        $patient->fill([
            'patient_uid'               => $request->patient_uid,
            'patient_fname'             => $request->patient_fname,
            'patient_lname'             => $request->patient_lname,
            'patient_age'               => $request->patient_age,
            'patient_gender'            => $request->patient_gender,
            'patient_email'             => $request->patient_email,
            'patient_phone'             => $request->patient_phone,
            'patient_marital_status'    => $request->patient_marital_status,
            'patient_occupation'        => $request->patient_occupation,
            'patient_blood_group'       => $request->patient_blood_group,
            'patient_blood_pressure'    => $request->patient_blood_pressure,
            'patient_sugar_level'       => $request->patient_sugar_level,
            'patient_address'           => $request->patient_address,
            'patient_city'              => $request->patient_city,
            'patient_state'             => $request->patient_state,
            'patient_postal_code'       => $request->patient_postal_code,
            'patient_status'            => '1'
        ]);

        $patient->save();
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        //Call protected method
        $filename = $this->storeImage($request->file('file'));

        return response()->json([
            'filename' => $filename
        ]);
    }

    protected function storeImage($file)
    {
        $filename = 'IMG-' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/patient'), $filename);
        return $filename;
    }

    private function deleteFile($filename)
    {
        if ($filename!='' && file_exists(public_path('/uploads/patient/'.$filename))) {
            @unlink(public_path('/uploads/patient/'.$filename));
        }
    }
}
