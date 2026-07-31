<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Get list of active articles
     */
    public function index()
    {
        $blogs = Blog::where('blog_status', '1')
            ->orderBy('blog_order', 'desc')
            ->get();

        $articles = $blogs->map(function ($blog) {
            return [
                'article_id' => $blog->blog_id,
                'title' => $blog->blog_title,
                'slug' => $blog->blog_slug,
                'date' => $blog->blog_date,
                'image_url' => $blog->blog_image ? asset('uploads/blog/' . $blog->blog_image) : '',
                'short_description' => $blog->blog_short_desc,
                'created_at' => $blog->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Articles fetched successfully',
            'data' => $articles
        ], 200);
    }

    /**
     * Get details of a single active article by ID
     */
    public function show($id)
    {
        $blog = Blog::where('blog_status', '1')
            ->where('blog_id', $id)
            ->first();

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found'
            ], 404);
        }

        $article = [
            'article_id' => $blog->blog_id,
            'title' => $blog->blog_title,
            'slug' => $blog->blog_slug,
            'date' => $blog->blog_date,
            'image_url' => $blog->blog_image ? asset('uploads/blog/' . $blog->blog_image) : '',
            'short_description' => $blog->blog_short_desc,
            'description' => $blog->blog_desc,
            'created_at' => $blog->created_at,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Article details fetched successfully',
            'data' => $article
        ], 200);
    }
}
