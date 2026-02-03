<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
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
                return $patient->patient_fname . ' ' . $patient->patient_lname;
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
            ->rawColumns(["checkbox", "gender", "image", "status", "action"])
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
