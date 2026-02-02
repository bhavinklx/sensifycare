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

        Session::flash('successMsg', 'Page added successfully');
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

        Session::flash('successMsg', 'Page updated successfully');
        return response()->json(['redirect_url' => route('pages-list')]);
    }

    public function view()
    {
        $pagesDetail = Pages::with('subPages')->where('page_parent', '0')->orderBy('page_order')->get();
        return view("admin.pages.list")->with('pagesDetail',$pagesDetail);
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
