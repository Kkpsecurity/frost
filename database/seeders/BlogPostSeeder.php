<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogPosts = [
            [
                'title' => 'Complete Guide to Florida Gun Laws 2025: What Every Gun Owner Should Know',
                'slug' => 'florida-gun-laws-2025',
                'excerpt' => 'Navigate Florida\'s complex gun laws with confidence. This comprehensive guide covers everything from purchasing requirements to concealed carry permits.',
                'content' => '<p>Comprehensive guide to Florida gun laws, covering purchasing requirements, concealed carry permits, and 2025 legislative changes.</p>',
                'meta_description' => 'Navigate Florida\'s complex gun laws with confidence. Complete guide to purchasing, permits, and legal requirements.',
                'author' => 'Security Law Expert',
                'category' => 'Gun Laws & Regulations',
                'tags' => ['Gun Laws', 'Florida', 'Regulations'],
                'read_time' => 8,
                'is_published' => true,
                'is_featured' => true,
                'published_at' => now()->subDays(11),
            ],
            [
                'title' => 'Essential Firearms Safety: Building Fundamental Skills for Security Professionals',
                'slug' => 'essential-firearms-safety',
                'excerpt' => 'Master the four fundamental rules of firearm safety and advanced handling techniques essential for security professionals.',
                'content' => '<p>Master the four fundamental rules of firearm safety and advanced handling techniques for security professionals.</p>',
                'meta_description' => 'Master firearm safety fundamentals and advanced handling techniques for security professionals.',
                'author' => 'Master Firearms Instructor',
                'category' => 'Weapons Training',
                'tags' => ['Firearms Safety', 'Training', 'Security'],
                'read_time' => 6,
                'is_published' => true,
                'is_featured' => true,
                'published_at' => now()->subDays(14),
            ],
            [
                'title' => 'Advanced Threat Assessment Techniques for Security Officers',
                'slug' => 'threat-assessment-techniques',
                'excerpt' => 'Learn professional threat assessment methodologies to identify and evaluate potential security risks effectively.',
                'content' => '<p>Professional threat assessment methodologies for identifying and evaluating potential security risks effectively.</p>',
                'meta_description' => 'Learn professional threat assessment methodologies for security officers to identify potential risks.',
                'author' => 'Security Assessment Specialist',
                'category' => 'Security Tips',
                'tags' => ['Threat Assessment', 'Security', 'Training'],
                'read_time' => 5,
                'is_published' => true,
                'is_featured' => true,
                'published_at' => now()->subDays(16),
            ],
        ];

        foreach ($blogPosts as $post) {
            \App\Models\BlogPost::create($post);
        }
    }
}
