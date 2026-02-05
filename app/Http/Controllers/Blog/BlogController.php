<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bcategory;
use App\Models\Blog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BlogController extends Controller
{
    function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
    }

    public function createSlug(Request $request)
    {
        $slug = Str::slug($request->blog_title);
        $allSlugs = $this->checkSlug($slug);

        if (! $allSlugs->contains('blog_slug', $slug)) {
            return response()->json(['slug' => $slug]);
        }

        for ($i = 1; $i <= 10; $i++) {
            $newSlug = $slug . '-' . $i;
            if (! $allSlugs->contains('blog_slug', $newSlug)) {
                return response()->json(['slug' => $newSlug]);
            }
        }
        return response()->json(['error' => 'Unable to generate unique slug'], 422);
    }

    protected function checkSlug($slug)
    {
        return Blog::select('blog_slug')
            ->where('blog_slug', 'like', $slug . '%')
            ->get();
    }

    public function create()
    {
        $bcategoryDetail = Bcategory::where("bcategory_status", "1")->get();
        return view("admin.blog.create", compact('bcategoryDetail'));
    }

    public function insert(Request $request)
    {
        $this->validateData($request);

        $blog = new Blog();
        $this->saveUpdateData($blog, $request);

        Session::flash('successMsg', 'Blog details added successfully');
        return response()->json(['redirect_url' => route('blog-list')]);
    }
    
    public function edit($id)
    {
        $blogDetail = Blog::find($id);
        $bcategoryDetail = Bcategory::where("bcategory_status", "1")->get();
        return view("admin.blog.edit", compact('blogDetail', 'bcategoryDetail'));
    }

    public function update(Request $request)
    {
        $this->validateData($request);

        $blog = Blog::findOrFail($request->blog_id);
        $this->saveUpdateData($blog, $request, true);

        Session::flash('successMsg', 'Blog details updated successfully');
        return response()->json(['redirect_url' => route('blog-list')]);
    }

    public function view()
    {
        return view("admin.blog.list");
    }

    public function load_table(Request $request)
    {
        $blogDetail = Blog::orderBy("blog_order");
        return DataTables::of($blogDetail)
            ->editColumn("checkbox", function ($blog){
                return '<div class="form-check m-0"> <input class="form-check-input check_class" type="checkbox" id="check[]" name="check[]" value="' . $blog->blog_id . '"> </div>';
            })
            ->editColumn("category", function ($blog){
                $categoryDetail = Bcategory::get();

                $categoryArray = [];
                foreach ($categoryDetail as $category) {
                    $categoryArray[$category->bcategory_id] = $category->bcategory_title;
                }

                return $categoryArray[$blog->bcategory_id] ?? "--";
            })
            ->editColumn("title", function ($blog){
                return $blog->blog_title;
            })
            ->editColumn("image", function ($blog){
                if($blog->blog_image!='' && file_exists(public_path('/uploads/blog/'.$blog->blog_image))){
                    return "<img src='".asset('/uploads/blog/'.$blog->blog_image)."' width='100px'>";
                } else {
                    return;
                }
            })
            ->editColumn("date", function ($blog){
                return date('d-m-Y h:i:s A', strtotime($blog->created_at));
            })
            ->editColumn("status", function ($blog) {
                if ($blog->blog_status == '1') {
                    return '<div id="td_status_' . $blog->blog_id . '"><a href="javascript:void(0)" onclick="change_status(' . $blog->blog_id . ',0)" ><span class="badge bg-success">Active</span></a></div>';
                } else {
                    return '<div id="td_status_' . $blog->blog_id . '"><a href="javascript:void(0)" onclick="change_status(' . $blog->blog_id . ',1)" ><span class="badge bg-danger">Inactive</span></a></div>';
                }
            })
            ->editColumn("action", function ($blog){
                $action = '<div class="d-inline-flex gap-1">';
                if (auth()->user()->can('blog-delete')) {
                    $action.= '<button class="btn btn-outline-danger btn-sm" onclick="deleteData(' . $blog->blog_id . ');" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete Blog"> <i class="ri-delete-bin-line"></i> </button>';
                }
                if (auth()->user()->can('blog-edit')) {
                    $action.= '<a href="'.route("blog-edit", ['id' => $blog->blog_id]).'" class="btn btn-outline-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Blog"> <i class="ri-edit-box-line"></i> </a>';
                }
                $action.= '</div>';
                return $action;
            })
            ->setRowClass(function () {
                return 'row1';
            })
            ->setRowAttr([
                "data-id" => function ($blog) {
                    return $blog->blog_id;
                }
            ])
            ->rawColumns(["checkbox", "image", "status", "popular_status", "action"])
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
            Blog::where("blog_id", $request->blog_id)->update(["blog_status" => $request->status]);
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
            Blog::where("blog_id", $order["blog_id"])->update(["blog_order" => $order["position"]]);
        }
        echo 'Blog order changed successfully.';
    }

    public function delete(Request $request)
    {
        $blog = Blog::findOrFail($request->blog_id);
        $this->deleteFile($blog->blog_image);

        $blog->delete();
        return response()->json(['status' => true,'message' => 'Blog deleted successfully.']);
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            "bcategory_id"              => "required|not_in:0",
            "blog_title"                => 'required|string|max:255',
            "blog_slug"                 => 'required|string|max:255',
            "blog_date"                 => "required"
        ]);
    }

    private function saveUpdateData(Blog $blog, Request $request, $isUpdate = false)
    {
        if ($request->hasFile('blog_image')) {
            if ($isUpdate) {
                $this->deleteFile($blog->blog_image);
            }
            $blog->blog_image           = $this->uploadFile($request->file('blog_image'));
        }

        //Dropzone async upload
        if ($request->blog_image) {
            $blog->blog_image           = $request->blog_image; // filename string
        }
       
        if ($isUpdate) {
            $blog->updated_at           = date('Y-m-d H:i:s');
        } else {
            $lastOrder                  = Blog::orderBy("blog_order", "DESC")->first();
            $blog->blog_order           = (!empty($lastOrder)) ? $lastOrder->blog_order + 1 : 1;
            $blog->created_at           = date('Y-m-d H:i:s');
        }
        
        $bcategoryDetail = Bcategory::where("bcategory_status", "1")->first();

        $blog->fill([
            'bcategory_id'              => $request->bcategory_id,
            'blog_title'                => $request->blog_title,
            'blog_slug'                 => $request->blog_slug,
            'blog_date'                 => date('Y-m-d',strtotime($request->blog_date)),
            'blog_short_desc'           => addslashes($request->blog_short_desc),
            'blog_desc'                 => $request->blog_desc,
            'blog_meta_title'           => $request->blog_meta_title,
            'blog_meta_keyword'         => $request->blog_meta_keyword,
            'blog_meta_desc'            => $request->blog_meta_desc,
            'blog_canonical'            => $request->blog_canonical,
            'blog_status'               => '1'
        ]);
   
        $blog->save();
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
        $file->move(public_path('uploads/blog'), $filename);
        return $filename;
    }

    private function deleteFile($filename)
    {
        if ($filename!='' && file_exists(public_path('/uploads/blog/'.$filename))) {
            @unlink(public_path('/uploads/blog/'.$filename));
        }
    }
}
