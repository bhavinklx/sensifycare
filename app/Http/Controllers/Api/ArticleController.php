<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Bcategory;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Get list of active articles
     */
    public function index(Request $request)
    {
        $categorySlug = $request->query('category', 'all');

        $query = Blog::where('blog_status', '1');
        $category = null;

        if ($categorySlug !== 'all') {
            $category = Bcategory::where('bcategory_status', '1')
                ->where(function($q) use ($categorySlug) {
                    $normalized = str_replace('_', '-', $categorySlug);
                    $normalizedUnderscore = str_replace('-', '_', $categorySlug);
                    $q->where('bcategory_slug', $categorySlug)
                      ->orWhere('bcategory_slug', $normalized)
                      ->orWhere('bcategory_slug', $normalizedUnderscore);
                })
                ->first();

            if ($category) {
                $query->where('bcategory_id', $category->bcategory_id);
            } else {
                $query->where('bcategory_id', 0);
            }
        }

        $perPage = $request->query('per_page', 10);
        $paginatedBlogs = $query->orderBy('blog_order', 'desc')
            ->orderBy('blog_date', 'desc')
            ->paginate($perPage);

        // Find featured article
        $featuredQuery = Blog::where('blog_status', '1')
            ->where('blog_popular_status', '1');
            
        if ($categorySlug !== 'all' && $category) {
            $featuredQuery->where('bcategory_id', $category->bcategory_id);
        }
        
        $featuredBlog = $featuredQuery->orderBy('blog_date', 'desc')->first();

        if (!$featuredBlog) {
            // Fallback: Latest active blog in the filtered/unfiltered list
            $fallbackQuery = Blog::where('blog_status', '1');
            if ($categorySlug !== 'all' && $category) {
                $fallbackQuery->where('bcategory_id', $category->bcategory_id);
            }
            $featuredBlog = $fallbackQuery->orderBy('blog_date', 'desc')->first();
        }

        $featuredArticle = null;
        if ($featuredBlog) {
            $featuredCategory = Bcategory::find($featuredBlog->bcategory_id);
            
            // Calculate read time
            $words = str_word_count(strip_tags($featuredBlog->blog_desc ?: ''));
            $readTimeMin = max(1, ceil($words / 200));
            $readTime = $readTimeMin . ' min read';

            $featuredArticle = [
                'article_id' => $featuredBlog->blog_id,
                'title' => $featuredBlog->blog_title,
                'slug' => $featuredBlog->blog_slug,
                'date' => $featuredBlog->blog_date,
                'image_url' => $featuredBlog->blog_image ? asset('uploads/blog/' . $featuredBlog->blog_image) : '',
                'short_description' => $featuredBlog->blog_short_desc,
                'category_name' => $featuredCategory ? $featuredCategory->bcategory_title : '',
                'read_time' => $readTime,
                'created_at' => $featuredBlog->created_at,
            ];
        }

        $articles = collect($paginatedBlogs->items())->map(function ($blog) {
            $bcategory = Bcategory::find($blog->bcategory_id);
            
            // Calculate read time
            $words = str_word_count(strip_tags($blog->blog_desc ?: ''));
            $readTimeMin = max(1, ceil($words / 200));
            $readTime = $readTimeMin . ' min';

            return [
                'article_id' => $blog->blog_id,
                'title' => $blog->blog_title,
                'slug' => $blog->blog_slug,
                'date' => $blog->blog_date,
                'image_url' => $blog->blog_image ? asset('uploads/blog/' . $blog->blog_image) : '',
                'short_description' => $blog->blog_short_desc,
                'category_name' => $bcategory ? $bcategory->bcategory_title : '',
                'label' => $blog->blog_popular_status == '1' ? 'Popular' : 'Essential Read',
                'read_time' => $readTime,
                'created_at' => $blog->created_at,
            ];
        });

        $categories = Bcategory::where('bcategory_status', '1')
            ->orderBy('bcategory_order', 'asc')
            ->get()
            ->map(function ($cat) {
                return [
                    'category_id' => $cat->bcategory_id,
                    'title' => $cat->bcategory_title,
                    'slug' => $cat->bcategory_slug,
                ];
            })
            ->toArray();

        array_unshift($categories, [
            'category_id' => 0,
            'title' => 'All',
            'slug' => 'all',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Articles fetched successfully',
            'data' => [
                'categories' => $categories,
                'featured_article' => $featuredArticle,
                'articles' => $articles,
                'pagination' => [
                    'current_page' => $paginatedBlogs->currentPage(),
                    'last_page' => $paginatedBlogs->lastPage(),
                    'per_page' => $paginatedBlogs->perPage(),
                    'total' => $paginatedBlogs->total(),
                ]
            ]
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
