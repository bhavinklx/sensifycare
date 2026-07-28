<?php

namespace App\Http\Controllers\HealthParameter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HealthParameter;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class HealthParameterController extends Controller
{
    function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
    }

    public function create()
    {
        return view("admin.health_parameter.create");
    }

    public function insert(Request $request)
    {
        $this->validateData($request);

        $healthParameter = new HealthParameter();
        $this->saveUpdateData($healthParameter, $request);

        Session::flash('successMsg', 'Health Parameter added successfully');
        return response()->json(['redirect_url' => route('health-parameter-list')]);
    }

    public function edit($id)
    {
        $healthParameterDetail = HealthParameter::findOrFail($id);
        return view("admin.health_parameter.edit", compact('healthParameterDetail'));
    }

    public function update(Request $request)
    {
        $this->validateData($request);

        $healthParameter = HealthParameter::findOrFail($request->health_parameter_id);
        $this->saveUpdateData($healthParameter, $request, true);

        Session::flash('successMsg', 'Health Parameter updated successfully');
        return response()->json(['redirect_url' => route('health-parameter-list')]);
    }

    public function view()
    {
        return view("admin.health_parameter.list");
    }

    public function load_table(Request $request)
    {
        $healthParameterDetail = HealthParameter::orderBy("health_parameter_order")->get();
        return DataTables::of($healthParameterDetail)
            ->editColumn("checkbox", function ($healthParameter) {
                return '<div class="form-check m-0"> <input class="form-check-input check_class" type="checkbox" id="check[]" name="check[]" value="' . $healthParameter->health_parameter_id . '"> </div>';
            })
            ->editColumn("name", function ($healthParameter) {
                return $healthParameter->health_parameter_name;
            })
            ->editColumn("question", function ($healthParameter) {
                return $healthParameter->health_parameter_question;
            })
            ->editColumn("show_type", function ($healthParameter) {
                return ucfirst($healthParameter->health_parameter_show_type);
            })
            ->editColumn("options", function ($healthParameter) {
                return implode(', ', $healthParameter->options_array);
            })
            ->editColumn("date", function ($healthParameter) {
                return date('d-m-Y h:i A', strtotime($healthParameter->created_at));
            })
            ->editColumn("status", function ($healthParameter) {
                if ($healthParameter->health_parameter_status == '1') {
                    return '<div id="td_status_' . $healthParameter->health_parameter_id . '"><a href="javascript:void(0)" onclick="change_status(' . $healthParameter->health_parameter_id . ',0)" ><span class="badge bg-success">Active</span></a></div>';
                } else {
                    return '<div id="td_status_' . $healthParameter->health_parameter_id . '"><a href="javascript:void(0)" onclick="change_status(' . $healthParameter->health_parameter_id . ',1)" ><span class="badge bg-danger">Inactive</span></a></div>';
                }
            })
            ->editColumn("action", function ($healthParameter) {
                $action = '<div class="d-inline-flex gap-1">';
                if (auth()->user()->can('health-parameter-delete')) {
                    $action .= '<button class="btn btn-outline-danger btn-sm" onclick="openDeleteModal(' . $healthParameter->health_parameter_id . ');" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete Health Parameter"> <i class="ri-delete-bin-line"></i> </button>';
                }
                if (auth()->user()->can('health-parameter-edit')) {
                    $action .= '<a href="' . route("health-parameter-edit", ['id' => $healthParameter->health_parameter_id]) . '" class="btn btn-outline-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Health Parameter"> <i class="ri-edit-box-line"></i> </a>';
                }
                $action .= '</div>';
                return $action;
            })
            ->setRowClass(function () {
                return 'row1';
            })
            ->setRowAttr([
                "id" => function ($healthParameter) {
                    return 'row_' . $healthParameter->health_parameter_id;
                },
                "data-id" => function ($healthParameter) {
                    return $healthParameter->health_parameter_id;
                }
            ])
            ->rawColumns(["checkbox", "status", "action"])
            ->make(true);
    }

    public function change_status(Request $request)
    {
        if (!$request->ajax()) {
            exit('No direct script access allowed');
        }
        if (!empty($request->all())) {
            HealthParameter::where("health_parameter_id", $request->health_parameter_id)->update(["health_parameter_status" => $request->status]);
            if ($request->status == 1) {
                echo 'Status Activated successfully';
            } else if ($request->status == 0) {
                echo 'Status Inactivated successfully';
            }
        }
    }

    public function update_order(Request $request)
    {
        foreach ($request->order as $order) {
            HealthParameter::where("health_parameter_id", $order["health_parameter_id"])->update(["health_parameter_order" => $order["position"]]);
        }
        echo 'Health Parameter order changed successfully.';
    }

    public function delete(Request $request)
    {
        $healthParameter = HealthParameter::findOrFail($request->health_parameter_id);
        $healthParameter->delete();
        return response('Health Parameter deleted successfully.');
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            "health_parameter_name" => 'required|string|max:255',
            "health_parameter_question" => 'required|string|max:255',
            "health_parameter_show_type" => 'required|in:dropdown,radio',
            "health_parameter_option1" => 'required|string|max:255',
            "health_parameter_option2" => 'nullable|string|max:255',
            "health_parameter_option3" => 'nullable|string|max:255',
            "health_parameter_option4" => 'nullable|string|max:255',
        ]);
    }

    private function saveUpdateData(HealthParameter $healthParameter, Request $request, $isUpdate = false)
    {
        if ($isUpdate) {
            $healthParameter->updated_at = now();
        } else {
            $lastOrder = HealthParameter::orderBy("health_parameter_order", "DESC")->first();
            $healthParameter->health_parameter_order = $lastOrder ? $lastOrder->health_parameter_order + 1 : 1;
            $healthParameter->created_at = now();
        }

        $healthParameter->fill([
            'health_parameter_name' => $request->health_parameter_name,
            'health_parameter_question' => $request->health_parameter_question,
            'health_parameter_show_type' => $request->health_parameter_show_type,
            'health_parameter_option1' => $request->health_parameter_option1,
            'health_parameter_option2' => $request->health_parameter_option2,
            'health_parameter_option3' => $request->health_parameter_option3,
            'health_parameter_option4' => $request->health_parameter_option4,
            'health_parameter_status' => $request->health_parameter_status ?? '1'
        ]);

        $healthParameter->save();
    }
}
