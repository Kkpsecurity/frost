<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display the main blog listing page
     */
    public function index()
    {
        return view('blog.index');
    }

    /**
     * Display blog list page (alternative to index for menu routing)
     */
    public function list()
    {
        return view('frontend.blog.index');
    }

    /**
     * Display individual blog posts
     */
    public function show($slug)
    {
        // Map blog post slugs to their corresponding views
        $blogPosts = [
            'florida-gun-laws-2025' => [
                'view' => 'frontend.blog.florida-gun-laws-2025',
                'title' => 'Complete Guide to Florida Gun Laws 2025: What Every Gun Owner Should Know',
                'meta_description' => 'Navigate Florida\'s complex gun laws with confidence. This comprehensive guide covers everything from purchasing requirements to concealed carry permits, recent legislative changes, and compliance requirements for 2025.',
                'category' => 'Gun Laws & Regulations',
                'date' => 'August 15, 2025',
                'author' => 'Security Law Expert',
                'read_time' => '8 min read'
            ],
            'essential-firearms-safety' => [
                'view' => 'frontend.blog.essential-firearms-safety',
                'title' => 'Essential Firearms Safety: Building Fundamental Skills for Security Professionals',
                'meta_description' => 'Master the four fundamental rules of firearm safety and advanced handling techniques essential for security professionals. Build lasting safety habits with professional training guidance.',
                'category' => 'Weapons Training',
                'date' => 'August 12, 2025',
                'author' => 'Master Firearms Instructor',
                'read_time' => '6 min read'
            ],
            'threat-assessment-techniques' => [
                'view' => 'frontend.blog.threat-assessment-techniques',
                'title' => 'Advanced Threat Assessment Techniques for Security Officers',
                'meta_description' => 'Learn professional threat assessment methodologies to identify and evaluate potential security risks effectively. Essential skills for modern security professionals.',
                'category' => 'Security Tips',
                'date' => 'August 10, 2025',
                'author' => 'Security Assessment Specialist',
                'read_time' => '5 min read'
            ],
            'security-license-renewal' => [
                'view' => 'frontend.blog.security-license-renewal',
                'title' => 'Security License Renewal: Complete Checklist for 2025',
                'meta_description' => 'Stay compliant with updated renewal requirements for Class D and Class G security licenses in Florida. Complete checklist and timeline for 2025 renewals.',
                'category' => 'Compliance & Licensing',
                'date' => 'August 8, 2025',
                'author' => 'Licensing Compliance Expert',
                'read_time' => '4 min read'
            ],
            'concealed-carry-florida' => [
                'view' => 'frontend.blog.concealed-carry-florida',
                'title' => 'Concealed Carry in Florida: Rights, Restrictions, and Responsibilities',
                'meta_description' => 'Understand your rights and responsibilities as a concealed carry permit holder in Florida, including recent constitutional carry changes and legal requirements.',
                'category' => 'Gun Laws & Regulations',
                'date' => 'August 5, 2025',
                'author' => 'Gun Rights Attorney',
                'read_time' => '7 min read'
            ],
            // Legacy blog posts from old system
            'security-training' => [
                'view' => 'frontend.blog.security-training',
                'title' => 'Comprehensive Security Training Programs in Florida',
                'meta_description' => 'Learn about comprehensive security training programs available in Florida, including Class D and Class G license requirements.',
                'category' => 'Security Training',
                'date' => 'August 1, 2025',
                'author' => 'Training Specialist',
                'read_time' => '5 min read'
            ],
            'security-officer' => [
                'view' => 'frontend.blog.security-officer',
                'title' => 'Security Officer Career Guide: Requirements and Opportunities',
                'meta_description' => 'Complete guide to becoming a security officer in Florida, including licensing requirements, career opportunities, and professional development.',
                'category' => 'Career Development',
                'date' => 'July 28, 2025',
                'author' => 'Career Advisor',
                'read_time' => '6 min read'
            ],
            'ensuring-compliance' => [
                'view' => 'frontend.blog.ensuring-compliance',
                'title' => 'Ensuring Compliance in Security Operations',
                'meta_description' => 'Essential guide to maintaining compliance in security operations, understanding regulations, and avoiding common violations.',
                'category' => 'Compliance & Licensing',
                'date' => 'July 25, 2025',
                'author' => 'Compliance Expert',
                'read_time' => '4 min read'
            ]
        ];

        // Check if the blog post exists
        if (!isset($blogPosts[$slug])) {
            abort(404);
        }

        $post = $blogPosts[$slug];

        return view($post['view'], compact('post'));
    }

    /**
     * Display blog posts by category
     */
    public function category($category)
    {
        $categories = [
            'gun-laws' => 'Gun Laws & Regulations',
            'weapons-training' => 'Weapons Training',
            'security-tips' => 'Security Tips',
            'compliance' => 'Compliance & Licensing'
        ];

        if (!isset($categories[$category])) {
            abort(404);
        }

        $categoryTitle = $categories[$category];

        return view('frontend.blog.category', compact('category', 'categoryTitle'));
    }

    /**
     * Handle newsletter subscription
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255'
        ]);

        // Here you would typically save the email to your newsletter database
        // and/or send it to your email marketing service

        // For now, we'll just return a success response
        return response()->json([
            'success' => true,
            'message' => 'Thank you for subscribing! You\'ll receive updates on security training and gun law changes.'
        ]);
    }

    /**
     * Search blog posts
     */
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return redirect()->route('blog.index');
        }

        // Here you would implement actual search functionality
        // For now, return the search results view with the query
        return view('blog.search', compact('query'));
    }

    /**
     * Display blog archive by date
     */
    public function archive($year, $month = null)
    {
        // Here you would implement archive functionality
        return view('blog.archive', compact('year', 'month'));
    }

    /**
     * Display posts by tag
     */
    public function tag($tag)
    {
        // Here you would implement tag functionality
        return view('blog.tag', compact('tag'));
    }

    /**
     * RSS feed for blog posts
     */
    public function rss()
    {
        // Here you would generate RSS feed
        return response()->view('blog.rss')->header('Content-Type', 'application/rss+xml');
    }

    /**
     * Sitemap for blog posts
     */
    public function sitemap()
    {
        // Here you would generate sitemap
        return response()->view('blog.sitemap')->header('Content-Type', 'application/xml');
    }
}
