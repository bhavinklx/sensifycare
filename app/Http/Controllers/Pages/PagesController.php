<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pages;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class PagesController extends Controller
{
    function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
    }

    public function createSlug(Request $request)
    {
        $slug = Str::slug($request->page_title);
        $allSlugs = $this->checkSlug($slug);

        if (! $allSlugs->contains('page_slug', $slug)) {
            return response()->json(['slug' => $slug]);
        }

        for ($i = 1; $i <= 10; $i++) {
            $newSlug = $slug . '-' . $i;
            if (! $allSlugs->contains('page_slug', $newSlug)) {
                return response()->json(['slug' => $newSlug]);
            }
        }
        return response()->json(['error' => 'Unable to generate unique slug'], 422);
    }

    protected function checkSlug($slug)
    {
        return Pages::select('page_slug')
            ->where('page_slug', 'like', $slug . '%')
            ->get();
    }

    public function create()
    {
        $parentPages = Pages::where(["page_status" => "1", "page_parent"=>"0"])->orderBy('page_order')->get();
        return view('admin.pages.create', compact('parentPages'));
    }

    public function insert(Request $request)
    {
        $this->validateData($request);

        $pages = new Pages();
        $this->saveUpdateData($pages, $request);

        Session::flash('successMsg', 'Pages details added successfully');
        return response()->json(['redirect_url' => route('pages-list')]);
    }

    public function edit($id)
    {
        $pagesDetail = Pages::find($id);
        $parentPages = Pages::where(["page_status" => "1", "page_parent"=>"0"])->orderBy('page_order')->get();
        //echo '<pre>'; print_r($parentPages); exit();
        return view("admin.pages.edit", compact('pagesDetail', 'parentPages'));
    }

    public function update(Request $request)
    {
        $this->validateData($request);

        $pages = Pages::findOrFail($request->page_id);
        $this->saveUpdateData($pages, $request, true);

        Session::flash('successMsg', 'Pages details updated successfully');
        return response()->json(['redirect_url' => route('pages-list')]);
    }

    public function view()
    {
        $pagesDetail = Pages::with('subPages')->where('page_parent', '0')->orderBy('page_order')->get();
        return view("admin.pages.list")->with('pagesDetail',$pagesDetail);
    }

    public function load_table(Request $request)
    {
        $pagesDetail = Pages::with('children');
        //return $pagesDetail;
        return DataTables::of($pagesDetail)
            ->editColumn("checkbox", function ($pages){
                return '<input type="checkbox" name="check[]" id="check[]" value="'.$pages->page_id.'" class="custom-checkbox check_class" />';
            })
            ->editColumn("title", function ($pages){
                //print_r($parent); exit();
                return $pages->page_title;
                /*if(count($pages->children) > 0) { foreach ($pages->children as $subpages) {
                    return $subpages->page_title;
                } }*/
            })
            ->editColumn("date", function ($pages){
                return date('d-m-Y h:i:s A', strtotime($pages->created_at));
            })
            ->editColumn("status", function ($pages){
                if ($pages->page_status == '1') {
                    return '<span id="td_status_'.$pages->page_id.'"><a href="javascript:void(0)" onclick="change_status('.$pages->page_id.', 0)" ><div class="label label-table label-success">Active</div></a></span>';
                } else {
                    return '<span id="td_status_'.$pages->page_id.'"><a href="javascript:void(0)" onclick="change_status('.$pages->page_id.', 1)" ><div class="label label-table label-danger">Inactive</div></a></span>';
                }
            })
            ->editColumn("header_status", function ($pages){
                if ($pages->page_header_status == '1') {
                    return '<span id="td_header_status_'.$pages->page_id.'"><a href="javascript:void(0)" onclick="change_header_status('.$pages->page_id.', 0)" ><div class="label label-table label-success">Active</div></a></span>';
                } else {
                    return '<span id="td_header_status_'.$pages->page_id.'"><a href="javascript:void(0)" onclick="change_header_status('.$pages->page_id.', 1)" ><div class="label label-table label-danger">Inactive</div></a></span>';
                }
            })
            ->editColumn("footer_status", function ($pages){
                if ($pages->page_footer_status == '1') {
                    return '<span id="td_footer_status_'.$pages->page_id.'"><a href="javascript:void(0)" onclick="change_footer_status('.$pages->page_id.', 0)" ><div class="label label-table label-success">Active</div></a></span>';
                } else {
                    return '<span id="td_footer_status_'.$pages->page_id.'"><a href="javascript:void(0)" onclick="change_footer_status('.$pages->page_id.', 1)" ><div class="label label-table label-danger">Inactive</div></a></span>';
                }
            })
            ->editColumn("action", function ($pages){
                $action = "";
                if ($pages->page_id == "1") {
                    if (auth()->user()->can('pages-edit')) {
                        $action.= '<a href="'.route("edit-pages", ['id' => $pages->page_id]).'" data-toggle="tooltip" data-placement="top" title="Edit"> <i class="fa fa-pencil text-inverse"></i> </a>';
                    }
                } else {
                    if (auth()->user()->can('pages-edit')) {
                        $action.= '<a href="'.route("edit-pages", ['id' => $pages->page_id]).'" data-toggle="tooltip" data-placement="top" title="Edit"> <i class="fa fa-pencil text-inverse"></i> </a>';
                    }
                    if (auth()->user()->can('pages-delete')) {
                        $action.= '<a href="javascript:void(0)" data-toggle="tooltip" onclick="deleteSingal(' . $pages->page_id . ');" data-placement="top" title="Delete"> <i class="fa fa-trash text-danger"></i> </a>';
                    }
                }
                return $action;
            })
            ->rawColumns(["checkbox", "status", "header_status", "footer_status", "action"])
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
            Pages::where('page_id', $request->page_id)->update(["page_status" => $request->status]);
            if ($request->status == 1) {
                echo 'Status Activate successfully';
            } else if ($request->status == 0){
                echo 'Status Inactivate successfully';
            }
        }
    }

    public function change_header_status(Request $request)
    {
        if (!$request->ajax())
        {
            exit('No direct script access allowed');
        }
        if (!empty($request->all()))
        {
            Pages::where('page_id', $request->page_id)->update(["page_header_status" => $request->status]);
            if ($request->status == 1) {
                echo 'Status Activate successfully';
            } else if ($request->status == 0){
                echo 'Status Inactivate successfully';
            }
        }
    }

    public function change_footer_status(Request $request)
    {
        if (!$request->ajax())
        {
            exit('No direct script access allowed');
        }
        if (!empty($request->all()))
        {
            Pages::where('page_id', $request->page_id)->update(["page_footer_status" => $request->status]);
            if ($request->status == 1) {
                echo 'Status Activate successfully';
            } else if ($request->status == 0){
                echo 'Status Inactivate successfully';
            }
        }
    }

    public function update_order(Request $request)
    {
        foreach ($request->order as $order)
        {
            Pages::where("page_id", $order["page_id"])->update(["page_order" => $order["position"]]);
        }
        echo 'Pages order changed successfully.';
    }

    public function delete(Request $request)
    {
        $pages = Pages::findOrFail($request->page_id);
        $this->deleteFile($pages->blog_image);

        $pages->delete();
        return response('Pages deleted successfully.');
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            "page_title"                => 'required|string|max:255',
            "page_slug"                 => 'required|string|max:255'
        ]);
    }

    private function saveUpdateData(Pages $pages, Request $request, $isUpdate = false)
    {
        if ($request->hasFile('page_image')) {
            if ($isUpdate && $pages->page_image) {
                $this->deleteFile($pages->page_image);
            }
            $pages->page_image          = $this->uploadImage($request->file('page_image'));
        }

        //Dropzone async upload
        if ($request->page_image) {
            $pages->page_image          = $request->page_image; // filename string
        }
   
        if ($isUpdate) {
            $pages->updated_at          = now();
        } else {
            $lastOrder = Pages::orderBy("page_order", "DESC")->first();
            $pages->page_order          = $lastOrder ? $lastOrder->page_order + 1 : 1;
            $pages->created_at          = now();
        }
        
        $pages->fill([
            'page_parent'               => $request->page_parent,
            'page_title'                => $request->page_title,
            'page_slug'                 => $request->page_slug,
            'page_link'                 => $request->page_link,
            'page_desc'                 => $request->page_desc,
            'page_meta_title'           => $request->page_meta_title,
            'page_meta_keyword'         => $request->page_meta_keyword,
            'page_meta_desc'            => $request->page_meta_desc,
            'page_status'               => $request->page_status,
            'page_header_status'        => $request->page_header_status,
            'page_footer_status'        => $request->page_footer_status,
        ]);

        $pages->save();
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
        $file->move(public_path('uploads/pages'), $filename);
        return $filename;
    }

    private function deleteFile($filename)
    {
        if ($filename!='' && file_exists(public_path('/uploads/pages/'.$filename))) {
            @unlink(public_path('/uploads/pages/'.$filename));
        }
    }
}
