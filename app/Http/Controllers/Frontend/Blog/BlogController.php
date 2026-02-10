<?php

namespace App\Http\Controllers\Frontend\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Display the main blog listing page
     */
    public function index(): View
    {
        $posts = BlogPost::published()
            ->recent()
            ->paginate(9); // 9 posts per page for 3x3 grid

        // Get categories for sidebar
        $categories = BlogPost::published()
            ->pluck('category')
            ->filter()
            ->unique()
            ->values();

        // Dynamic page content
        $pageData = [
            'title' => config('app.blog_title', 'Security Training & Legal Insights'),
            'description' => config('app.blog_description', 'Expert guidance on security training, firearms regulations, and professional development for security professionals'),
        ];

        return view('frontend.blog.index', compact('posts', 'categories', 'pageData'));
    }

    /**
     * Display blog list page (alternative to index for menu routing)
     */
    public function list(): View
    {
        return $this->index();
    }

    /**
     * Display individual blog posts
     */
    public function show(Request $request, BlogPost $blogPost)
    {
        // Ensure the post is published
        if (!$blogPost->is_published) {
            abort(404);
        }

        // Handle AJAX view increment
        if ($request->ajax() && $request->has('increment_views')) {
            $blogPost->incrementViews();
            return response()->json(['status' => 'success']);
        }

        // Increment view count for regular page loads
        if (!$request->ajax()) {
            $blogPost->incrementViews();
        }

        // Get related posts
        $relatedPosts = BlogPost::published()
            ->where('category', $blogPost->category)
            ->where('id', '!=', $blogPost->id)
            ->recent(3)
            ->get();

        // Get categories for sidebar
        $categories = BlogPost::published()
            ->pluck('category')
            ->filter()
            ->unique()
            ->values();

        return view('frontend.blog.show', compact('blogPost', 'relatedPosts', 'categories'))
            ->with('post', $blogPost); // Also pass as 'post' for template consistency
    }

    /**
     * Search blog posts
     */
    public function search(Request $request): View
    {
        $query = $request->get('q', '');

        $posts = BlogPost::published()
            ->when($query, function ($queryBuilder) use ($query) {
                $queryBuilder->where(function ($q) use ($query) {
                    $q->where('title', 'ILIKE', "%{$query}%")
                        ->orWhere('content', 'ILIKE', "%{$query}%")
                        ->orWhere('excerpt', 'ILIKE', "%{$query}%");
                });
            })
            ->recent()
            ->paginate(10);

        return view('frontend.blog.search', compact('posts', 'query'));
    }

    /**
     * Display posts by category
     */
    public function category(string $category): View
    {
        $posts = BlogPost::published()
            ->byCategory($category)
            ->recent()
            ->paginate(10);

        return view('frontend.blog.category', compact('posts', 'category'));
    }

    /**
     * Display posts by tag
     */
    public function tag(string $tag): View
    {
        $posts = BlogPost::published()
            ->whereJsonContains('tags', $tag)
            ->recent()
            ->paginate(10);

        return view('frontend.blog.tag', compact('posts', 'tag'));
    }

    /**
     * Archive posts by year and month
     */
    public function archive(int $year, ?int $month = null): View
    {
        $posts = BlogPost::published()
            ->whereYear('published_at', $year)
            ->when($month, function ($query) use ($month) {
                $query->whereMonth('published_at', $month);
            })
            ->recent()
            ->paginate(10);

        return view('frontend.blog.archive', compact('posts', 'year', 'month'));
    }

    /**
     * Subscribe to blog newsletter
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        // Here you would typically save to a newsletter table or send to a service
        // For now, just return a success response

        return response()->json([
            'success' => true,
            'message' => 'Successfully subscribed to our newsletter!'
        ]);
    }

    /**
     * Generate RSS feed
     */
    public function rss()
    {
        $posts = BlogPost::published()->recent(20)->get();

        return response()->view('frontend.blog.rss', compact('posts'))
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }

    /**
     * Generate sitemap for blog posts
     */
    public function sitemap()
    {
        $posts = BlogPost::published()->get();

        return response()->view('frontend.blog.sitemap', compact('posts'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
