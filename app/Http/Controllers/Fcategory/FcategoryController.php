<?php

namespace App\Http\Controllers\Fcategory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fcategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class FcategoryController extends Controller
{
    function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
    }

    public function createSlug(Request $request)
    {
        $slug = Str::slug($request->fcategory_title);
        $allSlugs = $this->checkSlug($slug);

        if (! $allSlugs->contains('fcategory_slug', $slug)) {
            return response()->json(['slug' => $slug]);
        }

        for ($i = 1; $i <= 10; $i++) {
            $newSlug = $slug . '-' . $i;
            if (! $allSlugs->contains('fcategory_slug', $newSlug)) {
                return response()->json(['slug' => $newSlug]);
            }
        }
        return response()->json(['error' => 'Unable to generate unique slug'], 422);
    }

    protected function checkSlug($slug)
    {
        return Fcategory::select('fcategory_slug')
            ->where('fcategory_slug', 'like', $slug . '%')
            ->get();
    }

    public function create()
    {
        return view("admin.fcategory.create");
    }

    public function insert(Request $request)
    {
        $this->validateData($request);

        $fcategory = new Fcategory();
        $this->saveUpdateData($fcategory, $request);

        Session::flash('successMsg', 'FAQ Category added successfully');
        return response()->json(['redirect_url' => route('fcategory-list')]);
    }

    public function edit($id)
    {
        $fcategoryDetail = Fcategory::findOrFail($id);
        return view("admin.fcategory.edit", compact('fcategoryDetail'));
    }

    public function update(Request $request)
    {
        $this->validateData($request);

        $fcategory = Fcategory::findOrFail($request->fcategory_id);
        $this->saveUpdateData($fcategory, $request, true);

        Session::flash('successMsg', 'FAQ Category updated successfully');
        return response()->json(['redirect_url' => route('fcategory-list')]);
    }

    public function view()
    {
        return view("admin.fcategory.list");
    }

    public function load_table(Request $request)
    {
        $fcategoryDetail = Fcategory::orderBy("fcategory_order")->get();
        return DataTables::of($fcategoryDetail)
            ->editColumn("checkbox", function ($fcategory) {
                return '<div class="form-check m-0"> <input class="form-check-input check_class" type="checkbox" id="check[]" name="check[]" value="' . $fcategory->fcategory_id . '"> </div>';
            })
            ->editColumn("title", function ($fcategory) {
                return $fcategory->fcategory_title;
            })
            ->editColumn("date", function ($fcategory) {
                return $fcategory->created_at ? date('d-m-Y h:i A', strtotime($fcategory->created_at)) : '-';
            })
            ->editColumn("status", function ($fcategory) {
                if ($fcategory->fcategory_status == '1') {
                    return '<div id="td_status_' . $fcategory->fcategory_id . '"><a href="javascript:void(0)" onclick="change_status(' . $fcategory->fcategory_id . ',0)"><span class="badge bg-success">Active</span></a></div>';
                } else {
                    return '<div id="td_status_' . $fcategory->fcategory_id . '"><a href="javascript:void(0)" onclick="change_status(' . $fcategory->fcategory_id . ',1)"><span class="badge bg-danger">Inactive</span></a></div>';
                }
            })
            ->editColumn("action", function ($fcategory) {
                $action = '<div class="d-inline-flex gap-1">';
                if (auth()->user()->can('fcategory-delete')) {
                    $action .= '<button class="btn btn-outline-danger btn-sm" onclick="openDeleteModal(' . $fcategory->fcategory_id . ');" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete Category"> <i class="ri-delete-bin-line"></i> </button>';
                }
                if (auth()->user()->can('fcategory-edit')) {
                    $action .= '<a href="' . route("fcategory-edit", ['id' => $fcategory->fcategory_id]) . '" class="btn btn-outline-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Category"> <i class="ri-edit-box-line"></i> </a>';
                }
                $action .= '</div>';
                return $action;
            })
            ->setRowClass(function () {
                return 'row1';
            })
            ->setRowAttr([
                "id" => function ($fcategory) {
                    return 'row_' . $fcategory->fcategory_id;
                },
                "data-id" => function ($fcategory) {
                    return $fcategory->fcategory_id;
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
            Fcategory::where("fcategory_id", $request->fcategory_id)->update(['fcategory_status' => $request->status]);
            if ($request->status == 1) {
                echo 'Status Activated successfully';
            } else if ($request->status == 0) {
                echo 'Status Inactivated successfully';
            }
        }
    }

    public function update_order(Request $request)
    {
        if (!empty($request->order)) {
            foreach ($request->order as $order) {
                Fcategory::where("fcategory_id", $order["fcategory_id"])->update(["fcategory_order" => $order["position"]]);
            }
        }
        echo 'FAQ Category order changed successfully.';
    }

    public function delete(Request $request)
    {
        Fcategory::where("fcategory_id", $request->fcategory_id)->delete();
        return response()->json(['success' => true]);
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            "fcategory_title" => 'required|string|max:255',
            "fcategory_slug"  => 'required|string|max:255'
        ]);
    }

    private function saveUpdateData(Fcategory $fcategory, Request $request, $isUpdate = false)
    {
        if ($isUpdate) {
            $fcategory->updated_at = date('Y-m-d H:i:s');
        } else {
            $lastOrder = Fcategory::orderBy("fcategory_order", "DESC")->first();
            $fcategory->fcategory_order = (!empty($lastOrder)) ? $lastOrder->fcategory_order + 1 : 1;
            $fcategory->created_at = date('Y-m-d H:i:s');
        }

        $fcategory->fill([
            'fcategory_title'  => $request->fcategory_title,
            'fcategory_slug'   => $request->fcategory_slug,
            'fcategory_status' => $request->fcategory_status ?? '1'
        ]);

        $fcategory->save();
    }
}
