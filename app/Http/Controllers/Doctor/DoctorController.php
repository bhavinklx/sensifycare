<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DoctorController extends Controller
{
    function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
    }
    
    public function create()
    {
        return view("admin.doctor.create");
    }

    public function insert(Request $request)
    {
        $this->validateData($request);

        $doctor = new Doctor();
        $this->saveUpdateData($doctor, $request);

        Session::flash('successMsg', 'Doctor added successfully');
        return response()->json(['redirect_url' => route('doctor-list')]);
    }
    
    public function edit($id)
    {
        $doctorDetail = Doctor::find($id);
        return view("admin.doctor.edit", compact('patientDetail'));
    }

    public function update(Request $request)
    {
        $this->validateData($request);

        $doctor = Doctor::findOrFail($request->doctor_id);
        $this->saveUpdateData($doctor, $request, true);

        Session::flash('successMsg', 'Doctor updated successfully');
        return response()->json(['redirect_url' => route('doctor-list')]);
    }

    public function view()
    {
        return view("admin.doctor.list");
    }

    public function load_table(Request $request)
    {
        $doctorDetail = Doctor::orderBy("doctor_order");
        return DataTables::of($doctorDetail)
            ->editColumn("checkbox", function ($doctor){
                return '<div class="form-check m-0"> <input class="form-check-input check_class" type="checkbox" id="check[]" name="check[]" value="' . $doctor->doctor_id . '"> </div>';
            })
            ->editColumn("uid", function ($doctor){
                return $doctor->doctor_uid;
            })
            ->editColumn("title", function ($doctor){
                return $doctor->doctor_fname . ' ' . $doctor->doctor_lname;
            })
            ->editColumn("email", function ($doctor){
                return $doctor->doctor_email;
            })
            ->editColumn("phone", function ($doctor){
                return $doctor->doctor_phone;
            })
            ->editColumn("gender", function ($doctor){
                if ($doctor->doctor_gender == 'male') {
                    return '<span class="badge bg-info-subtle text-info">'.ucwords($doctor->doctor_gender).'</span>';
                } else {
                    return '<span class="badge bg-warning-subtle text-warning">'.ucwords($doctor->doctor_gender).'</span>';
                }
            })
            ->editColumn("age", function ($doctor){
                return $doctor->doctor_age;
            })
            ->editColumn("blood_group", function ($doctor){
                return strtoupper($doctor->doctor_blood_group);
            })
            ->editColumn("date", function ($doctor){
                return date('d-m-Y h:i:s A', strtotime($doctor->created_at));
            })
            ->editColumn("status", function ($doctor) {
                if ($doctor->doctor_status == '1') {
                    return '<div id="td_status_' . $doctor->doctor_id . '"><a href="javascript:void(0)" onclick="change_status(' . $doctor->doctor_id . ',0)" ><span class="badge bg-success">Active</span></a></div>';
                } else {
                    return '<div id="td_status_' . $doctor->doctor_id . '"><a href="javascript:void(0)" onclick="change_status(' . $doctor->doctor_id . ',1)" ><span class="badge bg-danger">Inactive</span></a></div>';
                }
            })
            ->editColumn("action", function ($doctor){
                $action = '<div class="d-inline-flex gap-1">';
                if (auth()->user()->can('doctor-delete')) {
                    $action.= '<button class="btn btn-outline-danger btn-sm" onclick="openDeleteModal(' . $doctor->doctor_id . ');" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete Doctor"> <i class="ri-delete-bin-line"></i> </button>';
                }
                /*if (auth()->user()->can('doctor-edit')) {
                    $action.= '<a href="'.route("doctor-edit", ['id' => $doctor->doctor_id]).'" class="btn btn-outline-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Doctor"> <i class="ri-edit-box-line"></i> </a>';
                }*/
                $action.= '</div>';
                return $action;
            })
            ->setRowClass(function () {
                return 'row1';
            })
            ->setRowAttr([
                "id" => function ($doctor) {
                    return 'row_' . $doctor->doctor_id;
                },
                "data-id" => function ($doctor) {
                    return $doctor->doctor_id;
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
            Doctor::where("doctor_id", $request->doctor_id)->update(["doctor_status" => $request->status]);
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
            Doctor::where("doctor_id", $order["doctor_id"])->update(["doctor_order" => $order["position"]]);
        }
        echo 'Doctor order changed successfully.';
    }

    public function delete(Request $request)
    {
        $doctor = Doctor::findOrFail($request->doctor_id);
        $this->deleteFile($doctor->doctor_image);

        $doctor->delete();
        return response('Doctor deleted successfully.');
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            "doctor_fname"             => 'required|string|max:255',
            "doctor_lname"             => 'required|string|max:255',
            "doctor_email"             => 'required|string|max:255',
            "doctor_phone"             => "required"
        ]);
    }

    private function saveUpdateData(Doctor $doctor, Request $request, $isUpdate = false)
    {
        if ($request->hasFile('doctor_image')) {
            if ($isUpdate) {
                $this->deleteFile($doctor->doctor_image);
            }
            $doctor->doctor_image       = $this->uploadFile($request->file('doctor_image'));
        }

        //Dropzone async upload
        if ($request->doctor_image) {
            $doctor->doctor_image       = $request->doctor_image; // filename string
        }
        
        if ($isUpdate) {
            $doctor->updated_at         = date('Y-m-d H:i:s');
        } else {
            $lastOrder                  = Doctor::orderBy("doctor_order", "DESC")->first();
            $doctor->doctor_order       = (!empty($lastOrder)) ? $lastOrder->doctor_order + 1 : 1;
            $doctor->created_at         = date('Y-m-d H:i:s');
        }

        $doctor->fill([
            'doctor_uid'                => $request->doctor_uid,
            'doctor_fname'              => $request->doctor_fname,
            'doctor_lname'              => $request->doctor_lname,
            'doctor_age'                => $request->doctor_age,
            'doctor_gender'             => $request->doctor_gender,
            'doctor_email'              => $request->doctor_email,
            'doctor_phone'              => $request->doctor_phone,
            'doctor_marital_status'     => $request->doctor_marital_status,
            'doctor_blood_group'        => $request->doctor_blood_group,
            'doctor_qualification'      => $request->doctor_qualification,
            'doctor_designation'        => $request->doctor_designation,
            'doctor_address'            => $request->doctor_address,
            'doctor_city'               => $request->doctor_city,
            'doctor_state'              => $request->doctor_state,
            'doctor_postal_code'        => $request->doctor_postal_code,
            'doctor_status'             => '1'
        ]);

        $doctor->save();
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
        $file->move(public_path('uploads/doctor'), $filename);
        return $filename;
    }

    private function deleteFile($filename)
    {
        if ($filename!='' && file_exists(public_path('/uploads/doctor/'.$filename))) {
            @unlink(public_path('/uploads/doctor/'.$filename));
        }
    }
}
