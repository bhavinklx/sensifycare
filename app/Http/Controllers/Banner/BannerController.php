<?php

namespace App\Http\Controllers\Banner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BannerController extends Controller
{
    function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
    }

    public function create()
    {
        return view('admin.banner.create');
    }

    public function insert(Request $request)
    {
        $this->validateData($request);

        $banner = new Banner();
        $this->saveUpdateData($banner, $request);

        Session::flash('successMsg', 'Banner added successfully');
        return response()->json(['redirect_url' => route('banner-list')]);
    }

    public function edit($id)
    {
        $bannerDetail = Banner::findOrFail($id);
        return view('admin.banner.edit', compact('bannerDetail'));
    }

    public function update(Request $request)
    {
        $this->validateData($request);

        $banner = Banner::findOrFail($request->banner_id);
        $this->saveUpdateData($banner, $request, true);

        Session::flash('successMsg', 'Banner updated successfully');
        return response()->json(['redirect_url' => route('banner-list')]);
    }

    public function view()
    {
        return view('admin.banner.list');
    }

    public function load_table(Request $request)
    {
        //echo 'Hello'; exit();
        $bannerDetail = Banner::orderBy("banner_order")->get();
        return DataTables::of($bannerDetail)
            ->editColumn("checkbox", function ($banner){
                return '<div class="form-check m-0"> <input class="form-check-input check_class" type="checkbox" id="check[]" name="check[]" value="' . $banner->banner_id . '"> </div>';
            })
            ->editColumn("title", function ($banner){
                return $banner->banner_title;
            })
            ->editColumn("image", function ($banner){
                if($banner->banner_image!='' && file_exists(public_path('/uploads/banner/'.$banner->banner_image))){
                    return "<img src='".asset('/uploads/banner/'.$banner->banner_image)."' width='100px'>";
                } else {
                    return;
                }
            })
            ->editColumn("status", function ($banner) {
                if ($banner->blog_status == '1') {
                    return '<div id="td_status_' . $banner->banner_id . '"><a href="javascript:void(0)" onclick="change_status(' . $banner->banner_id . ',0)" ><span class="badge bg-success">Active</span></a></div>';
                } else {
                    return '<div id="td_status_' . $banner->banner_id . '"><a href="javascript:void(0)" onclick="change_status(' . $banner->banner_id . ',1)" ><span class="badge bg-danger">Inactive</span></a></div>';
                }
            })
            ->editColumn("action", function ($banner){
                $action = '<div class="d-inline-flex gap-1">';
                if (auth()->user()->can('banner-delete')) {
                    $action.= '<button class="btn btn-outline-danger btn-sm" onclick="openDeleteModal(' . $banner->banner_id . ');" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete Blog"> <i class="ri-delete-bin-line"></i> </button>';
                }
                if (auth()->user()->can('banner-edit')) {
                    $action.= '<a href="'.route("banner-edit", ['id' => $banner->banner_id]).'" class="btn btn-outline-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Blog"> <i class="ri-edit-box-line"></i> </a>';
                }
                $action.= '</div>';
                return $action;
            })
            //for add table row class
            ->setRowClass(function () {
                return 'row1';
            })
            //for add table row attr
            ->setRowAttr([
                "id" => function ($banner) {
                    return 'row_' . $banner->banner_id;
                },
                'data-id' => function($banner) {
                    return $banner->banner_id;
                }
            ])
            ->rawColumns(["checkbox", "image", "status", "action"])
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
            Banner::where("banner_id", $request->banner_id)->update(['banner_status' => $request->status]);
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
        foreach ($request->order as $order)
        {
            Banner::where("banner_id", $order["banner_id"])->update(["banner_order" => $order["position"]]);
        }
        echo 'Banner order changed successfully.';
    }

    public function delete(Request $request)
    {
        $banner = Banner::findOrFail($request->banner_id);
        $this->deleteFile($banner->banner_image);

        $banner->delete();
        return response('Banner deleted successfully.');
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            'banner_title'              => 'required|string|max:255'
        ]);
    }

    private function saveUpdateData(Banner $banner, Request $request, $isUpdate = false)
    {
        if ($request->hasFile('banner_image')) {
            if ($isUpdate) {
                $this->deleteFile($banner->banner_image);
            }
            $banner->banner_image       = $this->uploadFile($request->file('banner_image'));
        }

        //Dropzone async upload
        if ($request->banner_image) {
            $banner->banner_image       = $request->banner_image; // filename string
        }
        
        if ($isUpdate) {
            $banner->updated_at         = date('Y-m-d H:i:s');
        } else {
            $banner->created_at         = date('Y-m-d H:i:s');
        }

        $banner->fill([
            'banner_title'              => $request->banner_title,
            'banner_text'               => $request->banner_text,
            'banner_text1'              => $request->banner_text1,
            'banner_status'             => '1'
        ]);

        $banner->save();
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
        $file->move(public_path('uploads/banner'), $filename);
        return $filename;
    }

    private function deleteFile($filename)
    {
        if ($filename!='' && file_exists(public_path('/uploads/banner/'.$filename))) {
            @unlink(public_path('/uploads/banner/'.$filename));
        }
    }
}
