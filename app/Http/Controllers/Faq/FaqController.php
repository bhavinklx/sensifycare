<?php

namespace App\Http\Controllers\Faq;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fcategory;
use App\Models\Faq;
use App\Models\FaqItem;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class FaqController extends Controller
{
    function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
    }

    public function view()
    {
        $categories = Fcategory::where('fcategory_status', '1')->orderBy('fcategory_order')->get();
        return view('admin.faq.list', compact('categories'));
    }

    public function load_table(Request $request)
    {
        $query = Faq::with('category')->orderBy('faq_order', 'ASC');

        if ($request->filled('fcategory_id') && $request->fcategory_id != 'all') {
            $query->where('fcategory_id', $request->fcategory_id);
        }

        $faqs = $query->get();

        return DataTables::of($faqs)
            ->editColumn("checkbox", function ($faq) {
                return '<div class="form-check m-0"> <input class="form-check-input check_class" type="checkbox" id="check[]" name="check[]" value="' . $faq->faq_id . '"> </div>';
            })
            ->addColumn("category", function ($faq) {
                return $faq->category ? htmlspecialchars($faq->category->fcategory_title) : 'Uncategorized';
            })
            ->editColumn("status", function ($faq) {
                if ($faq->faq_status == '1') {
                    return '<div id="td_status_' . $faq->faq_id . '"><a href="javascript:void(0)" onclick="change_status(' . $faq->faq_id . ',0)"><span class="badge bg-success">Active</span></a></div>';
                } else {
                    return '<div id="td_status_' . $faq->faq_id . '"><a href="javascript:void(0)" onclick="change_status(' . $faq->faq_id . ',1)"><span class="badge bg-danger">Inactive</span></a></div>';
                }
            })
            ->editColumn("date", function ($faq) {
                return !empty($faq->created_at) ? date('d-m-Y h:i A', strtotime($faq->created_at)) : '-';
            })
            ->editColumn("action", function ($faq) {
                $action = '<div class="d-inline-flex gap-1">';
                if (auth()->user()->can('faq-delete')) {
                    $action .= '<button class="btn btn-outline-danger btn-sm" onclick="openDeleteModal(' . $faq->faq_id . ');" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete FAQ"> <i class="ri-delete-bin-line"></i> </button>';
                }
                if (auth()->user()->can('faq-edit')) {
                    $action .= '<a href="' . route("faq-edit", ['id' => $faq->faq_id]) . '" class="btn btn-outline-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit FAQ"> <i class="ri-edit-box-line"></i> </a>';
                }
                $action .= '</div>';
                return $action;
            })
            ->setRowClass(function () {
                return 'row1';
            })
            ->setRowAttr([
                "id" => function ($faq) {
                    return 'row_' . $faq->faq_id;
                },
                "data-id" => function ($faq) {
                    return $faq->faq_id;
                }
            ])
            ->rawColumns(["checkbox", "status", "action"])
            ->make(true);
    }

    public function create()
    {
        $categories = Fcategory::where('fcategory_status', '1')->orderBy('fcategory_order')->get();
        return view('admin.faq.create', compact('categories'));
    }

    public function insert(Request $request)
    {
        $request->validate([
            'fcategory_id' => 'required|exists:fcategory,fcategory_id',
        ], [
            'fcategory_id.required' => 'Please select a category.',
            'fcategory_id.exists'   => 'Selected category does not exist.',
        ]);

        // Collect valid questions and answers
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
        } elseif ($request->filled('faq_question') && $request->filled('faq_answer')) {
            $validItems[] = [
                'question' => trim($request->faq_question),
                'answer'   => trim($request->faq_answer),
            ];
        }

        if (empty($validItems)) {
            return response()->json([
                'errors' => ['faqs' => ['Please provide at least one Question and Answer.']]
            ], 422);
        }

        $lastOrderRecord = Faq::orderBy('faq_order', 'DESC')->first();
        $baseOrder = (!empty($lastOrderRecord)) ? ($lastOrderRecord->faq_order + 1) : 1;

        // 1. Create a single entry in faq table (only category, order, status, timestamps)
        $faq = new Faq();
        $faq->fcategory_id = $request->fcategory_id;
        $faq->faq_order    = $baseOrder;
        $faq->faq_status   = $request->faq_status ?? '1';
        $faq->created_at   = date('Y-m-d H:i:s');
        $faq->updated_at   = date('Y-m-d H:i:s');
        $faq->save();

        // 2. Insert questions & answers into child table faq_items
        foreach ($validItems as $index => $item) {
            FaqItem::create([
                'faq_id'         => $faq->faq_id,
                'faq_question'   => $item['question'],
                'faq_answer'     => $item['answer'],
                'faq_item_order' => $index + 1,
            ]);
        }

        $count = count($validItems);
        $message = ($count > 1) ? "FAQ added successfully with {$count} Questions & Answers" : 'FAQ added successfully';
        Session::flash('successMsg', $message);
        return response()->json(['redirect_url' => route('faq-list')]);
    }

    public function edit($id)
    {
        $faqDetail = Faq::with('items')->findOrFail($id);
        $categories = Fcategory::where('fcategory_status', '1')->orderBy('fcategory_order')->get();
        return view('admin.faq.edit', compact('faqDetail', 'categories'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'faq_id'       => 'required|exists:faq,faq_id',
            'fcategory_id' => 'required|exists:fcategory,fcategory_id',
        ], [
            'fcategory_id.required' => 'Please select a category.',
            'fcategory_id.exists'   => 'Selected category does not exist.',
        ]);

        $faq = Faq::findOrFail($request->faq_id);

        // Collect valid questions and answers
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
        } elseif ($request->filled('faq_question') && $request->filled('faq_answer')) {
            $validItems[] = [
                'question' => trim($request->faq_question),
                'answer'   => trim($request->faq_answer),
            ];
        }

        if (empty($validItems)) {
            return response()->json([
                'errors' => ['faqs' => ['Please provide at least one Question and Answer.']]
            ], 422);
        }

        // 1. Update single entry in faq table
        $faq->fcategory_id = $request->fcategory_id;
        $faq->faq_status   = $request->faq_status ?? '1';
        $faq->updated_at   = date('Y-m-d H:i:s');
        $faq->save();

        // 2. Sync child items in faq_items
        FaqItem::where('faq_id', $faq->faq_id)->delete();
        foreach ($validItems as $index => $item) {
            FaqItem::create([
                'faq_id'         => $faq->faq_id,
                'faq_question'   => $item['question'],
                'faq_answer'     => $item['answer'],
                'faq_item_order' => $index + 1,
            ]);
        }

        Session::flash('successMsg', 'FAQ updated successfully');
        return response()->json(['redirect_url' => route('faq-list')]);
    }

    public function change_status(Request $request)
    {
        if (!$request->ajax()) {
            exit('No direct script access allowed');
        }
        if (!empty($request->all())) {
            Faq::where('faq_id', $request->faq_id)->update(['faq_status' => $request->status]);
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
                Faq::where('faq_id', $order['faq_id'])->update(['faq_order' => $order['position']]);
            }
        }
        echo 'FAQ order changed successfully.';
    }

    public function delete(Request $request)
    {
        Faq::where('faq_id', $request->faq_id)->delete();
        FaqItem::where('faq_id', $request->faq_id)->delete();
        return response()->json(['success' => true]);
    }
}
