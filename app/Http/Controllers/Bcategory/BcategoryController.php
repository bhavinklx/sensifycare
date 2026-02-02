<?php

namespace App\Http\Controllers\Bcategory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bcategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BcategoryController extends Controller
{
    function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
    }

    public function createSlug(Request $request)
    {
        $slug = Str::slug($request->bcategory_title);
        $allSlugs = $this->checkSlug($slug);

        if (! $allSlugs->contains('bcategory_slug', $slug)) {
            return response()->json(['slug' => $slug]);
        }

        for ($i = 1; $i <= 10; $i++) {
            $newSlug = $slug . '-' . $i;
            if (! $allSlugs->contains('bcategory_slug', $newSlug)) {
                return response()->json(['slug' => $newSlug]);
            }
        }
        return response()->json(['error' => 'Unable to generate unique slug'], 422);
    }

    protected function checkSlug($slug)
    {
        return Bcategory::select('bcategory_slug')
            ->where('bcategory_slug', 'like', $slug . '%')
            ->get();
    }

    public function create()
    {
        return view("admin.bcategory.create");
    }

    public function insert(Request $request)
    {
        $this->validateData($request);

        $bcategory = new Bcategory();
        $this->saveUpdateData($bcategory, $request);

        Session::flash('successMsg', 'Category added successfully');
        return response()->json(['redirect_url' => route('bcategory-list')]);
    }

    public function edit($id)
    {
        $bcategoryDetail = Bcategory::find($id);
        return view("admin.bcategory.edit", compact('bcategoryDetail'));
    }

    public function update(Request $request)
    {
        $this->validateData($request);

        $bcategory = Bcategory::findOrFail($request->bcategory_id);
        $this->saveUpdateData($bcategory, $request, true);

        Session::flash('successMsg', 'Category updated successfully');
        return response()->json(['redirect_url' => route('bcategory-list')]);
    }

    public function view()
    {
        return view("admin.bcategory.list");
    }

    public function load_table(Request $request)
    {
        $bcategoryDetail = Bcategory::orderBy("bcategory_order")->get();
        return DataTables::of($bcategoryDetail)
            ->editColumn("checkbox", function ($bcategory){
                return '<div class="form-check m-0"> <input class="form-check-input check_class" type="checkbox" id="check[]" name="check[]" value="' . $bcategory->bcategory_id . '"> </div>';
            })
            ->editColumn("title", function ($bcategory) {
                return $bcategory->bcategory_title;
            })
            ->editColumn("date", function ($bcategory) {
                return date('d-m-Y h:i A', strtotime($bcategory->created_at));
            })
            ->editColumn("status", function ($bcategory) {
                if ($bcategory->bcategory_status == '1') {
                    return '<div id="td_status_' . $bcategory->bcategory_id . '"><a href="javascript:void(0)" onclick="change_status(' . $bcategory->bcategory_id . ',0)" ><span class="badge bg-success">Active</span></a></div>';
                } else {
                    return '<div id="td_status_' . $bcategory->bcategory_id . '"><a href="javascript:void(0)" onclick="change_status(' . $bcategory->bcategory_id . ',1)" ><span class="badge bg-danger">Inactive</span></a></div>';
                }
            })
            ->editColumn("action", function ($bcategory){
                $action = '<div class="d-inline-flex gap-1">';
                if (auth()->user()->can('bcategory-delete')) {
                    $action.= '<button class="btn btn-outline-danger btn-sm" onclick="openDeleteModal(' . $bcategory->bcategory_id . ');" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete Category"> <i class="ri-delete-bin-line"></i> </button>';
                }
                if (auth()->user()->can('bcategory-edit')) {
                    $action.= '<a href="'.route("bcategory-edit", ['id' => $bcategory->bcategory_id]).'" class="btn btn-outline-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Category"> <i class="ri-edit-box-line"></i> </a>';
                }
                $action.= '</div>';
                return $action;
            })
            ->setRowClass(function () {
                return 'row1';
            })
            ->setRowAttr([
                "id" => function ($bcategory) {
                    return 'row_' . $bcategory->bcategory_id;
                },
                "data-id" => function ($bcategory) {
                    return $bcategory->bcategory_id;
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
            Bcategory::where("bcategory_id", $request->bcategory_id)->update(['bcategory_status' => $request->status]);
            if ($request->status == 1) {
                echo 'Status Activate successfully';
            } else if ($request->status == 0) {
                echo 'Status Inactivate successfully';
            }
        }
    }

    public function update_order(Request $request)
    {
        //print_r($request->order); exit();
        foreach ($request->order as $order) {
            Bcategory::where("bcategory_id", $order["bcategory_id"])->update(["bcategory_order" => $order["position"]]);
        }
        echo 'Category order changed successfully.';
    }

    public function delete(Request $request)
    {
        Bcategory::where("bcategory_id", $request->bcategory_id)->delete();
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            "bcategory_title"           => 'required|string|max:255',
            "bcategory_slug"            => 'required|string|max:255'
        ]);

        return Validator::make($request->all(), [
            "bcategory_title" => 'required|string|max:255',
            "bcategory_slug" => 'required|string|max:255'
        ]);
    }
    
    private function saveUpdateData(Bcategory $bcategory, Request $request, $isUpdate = false)
    {
        if ($isUpdate) {
            $bcategory->updated_at      = date('Y-m-d H:i:s');
        } else {
            $bcategory->created_at      = date('Y-m-d H:i:s');
        }

        $bcategory->fill([
            'bcategory_title'           => $request->bcategory_title,
            'bcategory_slug'            => $request->bcategory_slug,
            'bcategory_meta_title'      => $request->bcategory_meta_title,
            'bcategory_meta_keyword'    => $request->bcategory_meta_keyword,
            'bcategory_meta_desc'       => $request->bcategory_meta_desc,
            'bcategory_status'          => '1' //$request->bcategory_status
        ]);

        $bcategory->save();
    }
}
