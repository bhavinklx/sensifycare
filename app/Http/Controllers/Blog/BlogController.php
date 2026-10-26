<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bcategory;
use App\Models\Blog;
use App\Models\Faq;
use App\Models\FaqItem;
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
        $bcategoryDetail = Bcategory::where("bcategory_status", "1")
            ->where(function($q) {
                $q->whereNull('bcategory_parent')->orWhere('bcategory_parent', 0);
            })
            ->orderBy('bcategory_order')
            ->get();
        return view("admin.blog.create", compact('bcategoryDetail'));
    }

    public function insert(Request $request)
    {
        $this->validateData($request);

        $blog = new Blog();
        $this->saveUpdateData($blog, $request);

        Session::flash('successMsg', 'Blog added successfully');
        return response()->json(['redirect_url' => route('blog-list')]);
    }
    
    public function edit($id)
    {
        $blogDetail = Blog::with(['faq.items'])->findOrFail($id);
        $bcategoryDetail = Bcategory::where("bcategory_status", "1")
            ->where(function($q) {
                $q->whereNull('bcategory_parent')->orWhere('bcategory_parent', 0);
            })
            ->orderBy('bcategory_order')
            ->get();

        $subCategories = [];
        if (!empty($blogDetail->bcategory_id)) {
            $subCategories = Bcategory::where('bcategory_status', '1')
                ->where('bcategory_parent', $blogDetail->bcategory_id)
                ->orderBy('bcategory_order')
                ->get();
        }

        return view("admin.blog.edit", compact('blogDetail', 'bcategoryDetail', 'subCategories'));
    }

    public function getSubcategories(Request $request)
    {
        $categoryId = $request->bcategory_id;
        $subcategories = [];
        if (!empty($categoryId)) {
            $subcategories = Bcategory::where('bcategory_status', '1')
                ->where('bcategory_parent', $categoryId)
                ->orderBy('bcategory_order')
                ->get(['bcategory_id', 'bcategory_title']);
        }
        return response()->json($subcategories);
    }

    public function update(Request $request)
    {
        $this->validateData($request);

        $blog = Blog::findOrFail($request->blog_id);
        $this->saveUpdateData($blog, $request, true);

        Session::flash('successMsg', 'Blog updated successfully');
        return response()->json(['redirect_url' => route('blog-list')]);
    }

    public function view()
    {
        return view("admin.blog.list");
    }

    public function detail($id)
    {
        $blog = Blog::with(['category', 'subCategory', 'faq.items'])->findOrFail($id);

        $imageUrl = '';
        if (!empty($blog->blog_image) && file_exists(public_path('/uploads/blog/' . $blog->blog_image))) {
            $imageUrl = asset('/uploads/blog/' . $blog->blog_image);
        }

        $formattedDate = !empty($blog->blog_date) ? date('d-m-Y', strtotime($blog->blog_date)) : date('d-m-Y', strtotime($blog->created_at));

        $faqs = [];
        if ($blog->faq && $blog->faq->items) {
            foreach ($blog->faq->items as $item) {
                $faqs[] = [
                    'question' => $item->faq_item_question,
                    'answer'   => $item->faq_item_answer,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'blog_id'           => $blog->blog_id,
                'blog_title'        => $blog->blog_title,
                'blog_slug'         => $blog->blog_slug,
                'blog_type'         => $blog->blog_type ?? 'image',
                'blog_date'         => $formattedDate,
                'category_name'     => $blog->category ? $blog->category->bcategory_title : '',
                'subcategory_name'  => $blog->subCategory ? $blog->subCategory->bcategory_title : '',
                'blog_image_url'    => $imageUrl,
                'blog_video_url'    => $blog->blog_video_url,
                'blog_short_desc'   => stripslashes($blog->blog_short_desc ?? ''),
                'blog_desc'         => $blog->blog_desc ?? '',
                'blog_meta_title'   => $blog->blog_meta_title,
                'blog_meta_keyword' => $blog->blog_meta_keyword,
                'blog_meta_desc'    => $blog->blog_meta_desc,
                'blog_canonical'    => $blog->blog_canonical,
                'blog_status'       => $blog->blog_status,
                'edit_url'          => route('blog-edit', ['id' => $blog->blog_id]),
                'faqs'              => $faqs,
            ]
        ]);
    }

    public function load_table(Request $request)
    {
        $blogDetail = Blog::with(['category', 'subCategory'])->orderBy("blog_order");
        return DataTables::of($blogDetail)
            ->editColumn("checkbox", function ($blog){
                return '<div class="form-check m-0"> <input class="form-check-input check_class" type="checkbox" id="check[]" name="check[]" value="' . $blog->blog_id . '"> </div>';
            })
            ->editColumn("category", function ($blog){
                $catName = $blog->category ? htmlspecialchars($blog->category->bcategory_title) : '--';
                if ($blog->subCategory) {
                    $subName = htmlspecialchars($blog->subCategory->bcategory_title);
                    return '<div><span class="fw-medium">' . $catName . '</span><div class="small text-muted"><i class="ri-corner-down-right-line me-1"></i>' . $subName . '</div></div>';
                }
                return '<span class="fw-medium">' . $catName . '</span>';
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
                $action.= '<button type="button" class="btn btn-outline-info btn-sm" onclick="openViewModal(' . $blog->blog_id . ');" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View Blog"> <i class="ri-eye-line"></i> </button>';
                if (auth()->user()->can('blog-delete')) {
                    $action.= '<button class="btn btn-outline-danger btn-sm" onclick="openDeleteModal(' . $blog->blog_id . ');" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete Blog"> <i class="ri-delete-bin-line"></i> </button>';
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
                "id" => function ($blog) {
                    return 'row_' . $blog->blog_id;
                },
                "data-id" => function ($blog) {
                    return $blog->blog_id;
                }
            ])
            ->rawColumns(["checkbox", "category", "image", "status", "popular_status", "action"])
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

        // Delete associated FAQs
        $faq = Faq::where('blog_id', $blog->blog_id)->first();
        if ($faq) {
            FaqItem::where('faq_id', $faq->faq_id)->delete();
            $faq->delete();
        }

        $blog->delete();
        return response('Blog deleted successfully.');
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            "bcategory_id"              => "required|not_in:0",
            "bsubcategory_id"           => "nullable",
            "blog_type"                 => "required|in:image,video",
            "blog_title"                => 'required|string|max:255',
            "blog_slug"                 => 'required|string|max:255',
            "blog_date"                 => "required",
            "blog_image"                => "nullable|string|max:255",
            "blog_video_url"            => "nullable|string|max:500"
        ]);
    }

    private function saveUpdateData(Blog $blog, Request $request, $isUpdate = false)
    {
        if ($request->hasFile('blog_image')) {
            if ($isUpdate && $blog->blog_image) {
                $this->deleteFile($blog->blog_image);
            }
            $blog->blog_image           = $this->storeImage($request->file('blog_image'));
        } else {
            // Dropzone async upload
            if ($request->filled('blog_image')) {
                // If update and new image is different from existing image, delete old file
                if ($isUpdate && $blog->blog_image && $blog->blog_image != $request->blog_image) {
                    $this->deleteFile($blog->blog_image);
                }
                $blog->blog_image       = $request->blog_image;
            } else {
                // If blog_image was removed / emptied
                if ($isUpdate && $blog->blog_image) {
                    $this->deleteFile($blog->blog_image);
                }
                $blog->blog_image       = null;
            }
        }
        
        if ($isUpdate) {
            $blog->updated_at           = date('Y-m-d H:i:s');
        } else {
            $lastOrder                  = Blog::orderBy("blog_order", "DESC")->first();
            $blog->blog_order           = (!empty($lastOrder)) ? $lastOrder->blog_order + 1 : 1;
            $blog->created_at           = date('Y-m-d H:i:s');
        }

        $blog->fill([
            'bcategory_id'              => $request->bcategory_id,
            'bsubcategory_id'           => $request->filled('bsubcategory_id') ? (int)$request->bsubcategory_id : 0,
            'blog_type'                 => $request->blog_type ?? 'image',
            'blog_title'                => $request->blog_title,
            'blog_slug'                 => $request->blog_slug,
            'blog_date'                 => date('Y-m-d', strtotime(str_replace('/', '-', $request->blog_date))),
            'blog_video_url'            => $request->blog_video_url,
            'blog_short_desc'           => addslashes($request->blog_short_desc),
            'blog_desc'                 => $request->blog_desc,
            'blog_meta_title'           => $request->blog_meta_title,
            'blog_meta_keyword'         => $request->blog_meta_keyword,
            'blog_meta_desc'            => $request->blog_meta_desc,
            'blog_canonical'            => $request->blog_canonical
            /*'blog_status'             => '1'*/
        ]);

        $blog->save();

        // Sync FAQs for blog
        $this->syncBlogFaqs($blog, $request);
    }

    private function syncBlogFaqs(Blog $blog, Request $request)
    {
        $validItems = [];
        if ($request->has('faqs') && is_array($request->faqs)) {
            foreach ($request->faqs as $item) {
                $question = trim($item['question'] ?? '');
                $answer   = trim($item['answer'] ?? '');
                if ($question !== '' && $answer !== '') {
                    $validItems[] = [
                        'question' => $question,
                        'answer'   => $answer,
                    ];
                }
            }
        }

        if (!empty($validItems)) {
            $faq = Faq::where('blog_id', $blog->blog_id)->first();
            if (!$faq) {
                $faq = new Faq();
                $faq->blog_id      = $blog->blog_id;
                $faq->fcategory_id = null; // No category when added from blog
                $faq->faq_order    = 0;
                $faq->faq_status   = '1';
                $faq->created_at   = date('Y-m-d H:i:s');
            }
            $faq->updated_at = date('Y-m-d H:i:s');
            $faq->save();

            // Sync child items
            FaqItem::where('faq_id', $faq->faq_id)->delete();
            foreach ($validItems as $index => $item) {
                FaqItem::create([
                    'faq_id'         => $faq->faq_id,
                    'faq_question'   => $item['question'],
                    'faq_answer'     => $item['answer'],
                    'faq_item_order' => $index + 1,
                ]);
            }
        } else {
            // Delete existing FAQ and items if all FAQs were cleared
            $faq = Faq::where('blog_id', $blog->blog_id)->first();
            if ($faq) {
                FaqItem::where('faq_id', $faq->faq_id)->delete();
                $faq->delete();
            }
        }
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

    public function deleteImage(Request $request)
    {
        if ($request->filename) {
            $this->deleteFile($request->filename);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 400);
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

    public function uploadEditorImage(Request $request)
    {
        $file = $request->file('upload') ?? $request->file('file') ?? $request->file('image');
        $funcNum = $request->input('CKEditorFuncNum');

        if ($file && $file->isValid()) {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
            $extension = strtolower($file->getClientOriginalExtension());

            if (!in_array($extension, $allowedExtensions)) {
                $errorMessage = 'Invalid image type. Allowed: jpg, jpeg, png, webp, gif, svg';
                if ($funcNum) {
                    $response = "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction({$funcNum}, '', '{$errorMessage}');</script>";
                    return response($response)->header('Content-Type', 'text/html; charset=utf-8');
                }
                return response()->json(['uploaded' => 0, 'error' => ['message' => $errorMessage]], 400);
            }

            $filename = 'EDITOR-' . time() . '_' . Str::random(8) . '.' . $extension;
            $destinationPath = public_path('uploads/editor');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $url = asset('uploads/editor/' . $filename);

            if ($funcNum) {
                $response = "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction({$funcNum}, '{$url}', '');</script>";
                return response($response)->header('Content-Type', 'text/html; charset=utf-8');
            }

            return response()->json([
                'uploaded' => 1,
                'fileName' => $filename,
                'url'      => $url,
            ]);
        }

        $errorMessage = 'No image uploaded or upload failed.';
        if ($funcNum) {
            $response = "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction({$funcNum}, '', '{$errorMessage}');</script>";
            return response($response)->header('Content-Type', 'text/html; charset=utf-8');
        }

        return response()->json(['uploaded' => 0, 'error' => ['message' => $errorMessage]], 400);
    }
}

