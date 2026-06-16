<?php

namespace App\Http\Controllers\Symptom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Symptom;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class SymptomController extends Controller
{
    function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
    }

    public function create()
    {
        return view("admin.symptoms.create");
    }

    public function insert(Request $request)
    {
        $this->validateData($request);

        $symptom = new Symptom();
        $this->saveUpdateData($symptom, $request);

        Session::flash('successMsg', 'Symptom added successfully');
        return response()->json(['redirect_url' => route('symptom-list')]);
    }

    public function edit($id)
    {
        $symptomDetail = Symptom::findOrFail($id);
        return view("admin.symptoms.edit", compact('symptomDetail'));
    }

    public function update(Request $request)
    {
        $this->validateData($request);

        $symptom = Symptom::findOrFail($request->symptom_id);
        $this->saveUpdateData($symptom, $request, true);

        Session::flash('successMsg', 'Symptom updated successfully');
        return response()->json(['redirect_url' => route('symptom-list')]);
    }

    public function view()
    {
        return view("admin.symptoms.list");
    }

    public function load_table(Request $request)
    {
        $symptomDetail = Symptom::orderBy("symptom_order")->get();
        return DataTables::of($symptomDetail)
            ->editColumn("checkbox", function ($symptom) {
                return '<div class="form-check m-0"> <input class="form-check-input check_class" type="checkbox" id="check[]" name="check[]" value="' . $symptom->symptom_id . '"> </div>';
            })
            ->editColumn("title", function ($symptom) {
                return $symptom->symptom_name;
            })
            ->editColumn("description", function ($symptom) {
                return $symptom->symptom_desc;
            })
            ->editColumn("image", function ($symptom) {
                if ($symptom->symptom_image != '' && file_exists(public_path('/uploads/symptom/' . $symptom->symptom_image))) {
                    return "<img src='" . asset('/uploads/symptom/' . $symptom->symptom_image) . "' width='50px'>";
                }
                return '';
            })
            ->editColumn("date", function ($symptom) {
                return date('d-m-Y h:i A', strtotime($symptom->created_at));
            })
            ->editColumn("status", function ($symptom) {
                if ($symptom->symptom_status == '1') {
                    return '<div id="td_status_' . $symptom->symptom_id . '"><a href="javascript:void(0)" onclick="change_status(' . $symptom->symptom_id . ',0)" ><span class="badge bg-success">Active</span></a></div>';
                } else {
                    return '<div id="td_status_' . $symptom->symptom_id . '"><a href="javascript:void(0)" onclick="change_status(' . $symptom->symptom_id . ',1)" ><span class="badge bg-danger">Inactive</span></a></div>';
                }
            })
            ->editColumn("action", function ($symptom) {
                $action = '<div class="d-inline-flex gap-1">';
                if (auth()->user()->can('symptom-delete')) {
                    $action .= '<button class="btn btn-outline-danger btn-sm" onclick="openDeleteModal(' . $symptom->symptom_id . ');" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete Symptom"> <i class="ri-delete-bin-line"></i> </button>';
                }
                if (auth()->user()->can('symptom-edit')) {
                    $action .= '<a href="' . route("symptom-edit", ['id' => $symptom->symptom_id]) . '" class="btn btn-outline-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Symptom"> <i class="ri-edit-box-line"></i> </a>';
                }
                $action .= '</div>';
                return $action;
            })
            ->setRowClass(function () {
                return 'row1';
            })
            ->setRowAttr([
                "id" => function ($symptom) {
                    return 'row_' . $symptom->symptom_id;
                },
                "data-id" => function ($symptom) {
                    return $symptom->symptom_id;
                }
            ])
            ->rawColumns(["checkbox", "image", "status", "action"])
            ->make(true);
    }

    public function change_status(Request $request)
    {
        if (!$request->ajax()) {
            exit('No direct script access allowed');
        }
        if (!empty($request->all())) {
            Symptom::where("symptom_id", $request->symptom_id)->update(["symptom_status" => $request->status]);
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
            Symptom::where("symptom_id", $order["symptom_id"])->update(["symptom_order" => $order["position"]]);
        }
        echo 'Symptom order changed successfully.';
    }

    public function delete(Request $request)
    {
        $symptom = Symptom::findOrFail($request->symptom_id);
        $this->deleteFile($symptom->symptom_image);
        $symptom->delete();
        return response('Symptom deleted successfully.');
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            "symptom_name" => 'required|string|max:255',
            "symptom_desc" => 'required|string',
        ]);
    }

    private function saveUpdateData(Symptom $symptom, Request $request, $isUpdate = false)
    {
        if ($request->hasFile('symptom_image')) {
            if ($isUpdate && $symptom->symptom_image) {
                $this->deleteFile($symptom->symptom_image);
            }
            $symptom->symptom_image     = $this->uploadImage($request->file('symptom_image'));
        }

        //Dropzone async upload
        if ($request->symptom_image) {
            $symptom->symptom_image     = $request->symptom_image; // filename string
        }

        if ($isUpdate) {
            $symptom->updated_at        = now();
        } else {
            $lastOrder                  = Symptom::orderBy("symptom_order", "DESC")->first();
            $symptom->symptom_order     = $lastOrder ? $lastOrder->symptom_order + 1 : 1;
            $symptom->created_at        = now();
        }

        $symptom->fill([
            'symptom_name'              => $request->symptom_name,
            'symptom_desc'              => $request->symptom_desc,
            'symptom_status'            => $request->symptom_status ?? '1'
        ]);

        $symptom->save();
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
        $file->move(public_path('uploads/symptom'), $filename);
        return $filename;
    }

    private function deleteFile($filename)
    {
        if ($filename!='' && file_exists(public_path('/uploads/symptom/'.$filename))) {
            @unlink(public_path('/uploads/symptom/'.$filename));
        }
    }
}
