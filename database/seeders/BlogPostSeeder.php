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
            [
                'title' => 'How to Obtain Your Florida Class D Security License: Step-by-Step Guide',
                'slug' => 'florida-class-d-security-license-guide',
                'excerpt' => 'Everything you need to know about getting your Florida Class D unarmed security license — from training requirements to the FDLE application process.',
                'content' => '<p>Step-by-step walkthrough of the Class D security license process in Florida, covering the 40-hour training requirement, background check, and FDLE application.</p>',
                'meta_description' => 'Step-by-step guide to obtaining your Florida Class D security officer license including training and FDLE requirements.',
                'author' => 'Florida Security Training Team',
                'category' => 'Licensing & Certification',
                'tags' => ['Class D', 'Florida', 'Security License', 'FDLE'],
                'read_time' => 7,
                'is_published' => true,
                'is_featured' => false,
                'published_at' => now()->subDays(20),
            ],
            [
                'title' => 'Class G Statewide Firearms License: What You Need to Know',
                'slug' => 'florida-class-g-firearms-license',
                'excerpt' => 'A complete overview of Florida\'s Class G statewide firearms license — who needs it, training requirements, and how to maintain your license.',
                'content' => '<p>Complete overview of the Class G statewide firearms license for armed security officers in Florida, including the 28-hour training requirement and annual requalification.</p>',
                'meta_description' => 'Complete overview of Florida Class G statewide firearms license requirements, training, and renewal.',
                'author' => 'Master Firearms Instructor',
                'category' => 'Licensing & Certification',
                'tags' => ['Class G', 'Firearms License', 'Florida', 'Armed Security'],
                'read_time' => 6,
                'is_published' => true,
                'is_featured' => false,
                'published_at' => now()->subDays(25),
            ],
            [
                'title' => 'Situational Awareness: The Most Critical Skill for Security Professionals',
                'slug' => 'situational-awareness-security-professionals',
                'excerpt' => 'Situational awareness is the foundation of effective security work. Learn the techniques professionals use to stay alert and ahead of potential threats.',
                'content' => '<p>An in-depth look at situational awareness — how to develop it, practice it, and apply it on the job as a security officer in Florida.</p>',
                'meta_description' => 'Learn how security professionals develop and apply situational awareness to prevent incidents before they escalate.',
                'author' => 'Security Assessment Specialist',
                'category' => 'Security Tips',
                'tags' => ['Situational Awareness', 'Security Tips', 'Training'],
                'read_time' => 5,
                'is_published' => true,
                'is_featured' => false,
                'published_at' => now()->subDays(30),
            ],
        ];

        foreach ($blogPosts as $post) {
            \App\Models\BlogPost::updateOrCreate(['slug' => $post['slug']], $post);
        }
    }
}
