<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pages;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Get list of active pages (only title and description)
     */
    public function index()
    {
        $pages = Pages::where('page_status', '1')
            ->orderBy('page_order', 'asc')
            ->get()
            ->map(function ($page) {
                return [
                    'title' => $page->page_title,
                    'slug' => $page->page_slug,
                    'url' => url('/' . $page->page_slug),
                    'description' => $page->page_desc,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Pages fetched successfully',
            'data' => $pages
        ], 200);
    }

    /**
     * Display the Privacy Policy static HTML page
     */
    public function privacyPolicy()
    {
        return view('static.privacy-policy');
    }

    /**
     * Display the Help and Support static HTML page
     */
    public function helpSupport()
    {
        return view('static.help-and-support');
    }
}
