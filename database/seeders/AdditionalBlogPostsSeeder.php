<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class AdditionalBlogPostsSeeder extends Seeder
{
    public function run()
    {
        // Gun Laws & Legal Updates
        BlogPost::create([
            'title' => 'Florida Firearm Laws Update 2025: What Security Officers Need to Know',
            'slug' => 'florida-firearm-laws-2025-update',
            'excerpt' => 'Stay informed about the latest changes to Florida firearm laws and how they affect security officers. Key updates for 2025 including concealed carry regulations and training requirements.',
            'content' => $this->getLegalUpdateContent(),
            'featured_image' => 'images/Security-Page-1.jpg',
            'author' => 'Legal Compliance Team',
            'category' => 'Legal & Compliance',
            'tags' => json_encode([
                'Gun Laws',
                'Florida Regulations',
                'Legal Updates',
                'Concealed Carry',
                'Security Officer Law'
            ]),
            'meta_description' => 'Complete guide to Florida firearm law changes in 2025 affecting security officers and concealed carry permit holders.',
            'read_time' => 6,
            'published_at' => now()->subDays(3),
            'is_published' => true
        ]);

        // Weapons Training
        BlogPost::create([
            'title' => 'Essential Weapons Safety Training for Armed Security Guards',
            'slug' => 'essential-weapons-safety-training-armed-security',
            'excerpt' => 'Master the fundamentals of weapons safety with our comprehensive guide for armed security professionals. Learn proper handling, storage, and deployment techniques.',
            'content' => $this->getWeaponsSafetyContent(),
            'featured_image' => 'images/Security-Page-1.jpg',
            'author' => 'Firearms Instructor',
            'category' => 'Weapons & Safety',
            'tags' => json_encode([
                'Weapons Training',
                'Firearm Safety',
                'Armed Security',
                'Safety Protocols',
                'Professional Training'
            ]),
            'meta_description' => 'Professional weapons safety training guide for armed security guards covering handling, storage, and deployment techniques.',
            'read_time' => 7,
            'published_at' => now()->subDays(7),
            'is_published' => true
        ]);

        // Industry News
        BlogPost::create([
            'title' => 'Security Industry Trends 2025: Technology and Training Evolution',
            'slug' => 'security-industry-trends-2025',
            'excerpt' => 'Explore the latest trends shaping the security industry in 2025, from advanced surveillance technology to evolving training methodologies.',
            'content' => $this->getIndustryTrendsContent(),
            'featured_image' => 'images/Security-Page-1.jpg',
            'author' => 'Industry Analyst',
            'category' => 'Industry News',
            'tags' => json_encode([
                'Industry Trends',
                'Security Technology',
                'Professional Development',
                'Career Growth',
                'Innovation'
            ]),
            'meta_description' => 'Stay ahead of security industry trends in 2025 including technology advancements and training evolution.',
            'read_time' => 5,
            'published_at' => now()->subDays(14),
            'is_published' => true
        ]);

        // Training Certification
        BlogPost::create([
            'title' => 'Complete Guide to Class D Security Officer Certification in Florida',
            'slug' => 'class-d-security-officer-certification-florida',
            'excerpt' => 'Everything you need to know about obtaining your Class D security officer license in Florida, including requirements, training, and career opportunities.',
            'content' => $this->getClassDGuideContent(),
            'featured_image' => 'images/Security-Page-1.jpg',
            'author' => 'Training Coordinator',
            'category' => 'Training & Certification',
            'tags' => json_encode([
                'Class D License',
                'Security Certification',
                'Florida Requirements',
                'Unarmed Security',
                'Career Guidance'
            ]),
            'meta_description' => 'Comprehensive guide to Class D security officer certification in Florida including requirements, training, and career prospects.',
            'read_time' => 8,
            'published_at' => now()->subDays(21),
            'is_published' => true
        ]);
    }

    private function getLegalUpdateContent()
    {
        return '<div class="blog-content">
            <div class="featured-image text-center mb-4">
                <img src="/images/Security-Page-1.jpg" alt="Florida Firearm Laws Update" class="img-fluid rounded shadow">
            </div>

            <p class="lead">Florida\'s firearm laws continue to evolve, and security officers must stay current with these changes to maintain compliance and professional standards. Here\'s what you need to know for 2025.</p>

            <h2 class="mt-4 mb-3">Key Changes in 2025</h2>
            <div class="alert alert-info">
                <p><strong>Important:</strong> New regulations affecting concealed carry permits and security officer licensing took effect January 1, 2025.</p>
            </div>

            <h3>Concealed Carry Updates</h3>
            <ul class="mb-4">
                <li>Enhanced training requirements for permit renewal</li>
                <li>Updated background check procedures</li>
                <li>New reciprocity agreements with neighboring states</li>
                <li>Modified carry restrictions in certain venues</li>
            </ul>

            <h3>Security Officer Implications</h3>
            <p>These changes directly impact how security officers carry and use firearms in their professional capacity. Understanding these updates is crucial for maintaining legal compliance.</p>

            <div class="cta-section text-center bg-primary text-white p-4 rounded mt-4">
                <h3>Need Legal Compliance Training?</h3>
                <p>Stay compliant with our updated legal training courses.</p>
                <a href="/courses" class="btn btn-warning btn-lg">View Legal Courses</a>
            </div>
        </div>';
    }

    private function getWeaponsSafetyContent()
    {
        return '<div class="blog-content">
            <div class="featured-image text-center mb-4">
                <img src="/images/Security-Page-1.jpg" alt="Weapons Safety Training" class="img-fluid rounded shadow">
            </div>

            <p class="lead">Proper weapons safety training is the foundation of professional armed security work. Master these essential principles to ensure safety and effectiveness.</p>

            <h2 class="mt-4 mb-3">The Four Fundamental Rules</h2>
            <div class="training-section bg-light p-4 rounded mb-4">
                <ol>
                    <li class="mb-2"><strong>Treat every firearm as if it\'s loaded</strong></li>
                    <li class="mb-2"><strong>Never point the muzzle at anything you don\'t intend to destroy</strong></li>
                    <li class="mb-2"><strong>Keep your finger off the trigger until ready to shoot</strong></li>
                    <li class="mb-2"><strong>Be sure of your target and what\'s beyond it</strong></li>
                </ol>
            </div>

            <h2 class="mt-4 mb-3">Professional Storage and Maintenance</h2>
            <p>Armed security officers must understand proper weapon storage, maintenance, and deployment procedures specific to their work environment.</p>

            <h3>Key Training Areas</h3>
            <ul class="mb-4">
                <li>Holster selection and proper draw techniques</li>
                <li>Equipment maintenance and inspection</li>
                <li>Situational awareness and threat assessment</li>
                <li>De-escalation techniques</li>
                <li>Legal considerations for use of force</li>
            </ul>

            <div class="cta-section text-center bg-dark text-white p-4 rounded mt-4">
                <h3>Ready for Weapons Training?</h3>
                <p>Enroll in our comprehensive firearms safety course.</p>
                <a href="/courses" class="btn btn-success btn-lg">Start Training</a>
            </div>
        </div>';
    }

    private function getIndustryTrendsContent()
    {
        return '<div class="blog-content">
            <div class="featured-image text-center mb-4">
                <img src="/images/Security-Page-1.jpg" alt="Security Industry Trends" class="img-fluid rounded shadow">
            </div>

            <p class="lead">The security industry is rapidly evolving with new technologies, training methodologies, and professional standards. Stay ahead of these trends to advance your career.</p>

            <h2 class="mt-4 mb-3">Technology Integration</h2>
            <div class="row align-items-center mb-4">
                <div class="col-md-8">
                    <p>Modern security operations increasingly rely on advanced technology including AI-powered surveillance, mobile patrol management systems, and integrated communication platforms.</p>
                </div>
            </div>

            <h3>Training Evolution</h3>
            <ul class="mb-4">
                <li>Virtual reality training simulations</li>
                <li>Online certification programs</li>
                <li>Continuous professional development</li>
                <li>Specialized skill certifications</li>
            </ul>

            <h2 class="mt-4 mb-3">Career Growth Opportunities</h2>
            <p>The evolving industry creates new pathways for professional advancement and specialization in security careers.</p>

            <div class="highlight-section bg-info text-white p-4 rounded mb-4">
                <h3 class="text-white">Future-Ready Skills</h3>
                <p class="mb-0">Technology proficiency, advanced communication skills, and specialized training certifications are becoming essential for career advancement.</p>
            </div>
        </div>';
    }

    private function getClassDGuideContent()
    {
        return '<div class="blog-content">
            <div class="featured-image text-center mb-4">
                <img src="/images/Security-Page-1.jpg" alt="Class D Security License" class="img-fluid rounded shadow">
            </div>

            <p class="lead">The Class D security officer license is your entry point into Florida\'s security industry. This comprehensive guide covers everything you need to know to get started.</p>

            <h2 class="mt-4 mb-3">Licensing Requirements</h2>
            <div class="training-section bg-light p-4 rounded mb-4">
                <h3>Basic Qualifications</h3>
                <ul>
                    <li class="mb-2">Must be at least 18 years old</li>
                    <li class="mb-2">High school diploma or equivalent</li>
                    <li class="mb-2">Pass background investigation</li>
                    <li class="mb-2">Complete required training hours</li>
                    <li class="mb-2">Pass state examination</li>
                </ul>
            </div>

            <h2 class="mt-4 mb-3">Training Requirements</h2>
            <p>Class D licenses require completion of 40 hours of professional security training covering:</p>

            <div class="row">
                <div class="col-md-6">
                    <ul>
                        <li>Legal aspects of security</li>
                        <li>Report writing</li>
                        <li>Public relations</li>
                        <li>Emergency procedures</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul>
                        <li>Patrol techniques</li>
                        <li>Crime prevention</li>
                        <li>Communication skills</li>
                        <li>Professional ethics</li>
                    </ul>
                </div>
            </div>

            <h2 class="mt-4 mb-3">Career Opportunities</h2>
            <p>Class D license holders can work in various security positions including retail security, office building security, event security, and more.</p>

            <div class="cta-section text-center bg-success text-white p-4 rounded mt-4">
                <h3>Start Your Security Career Today</h3>
                <p>Enroll in our Class D certification program and begin your professional journey.</p>
                <a href="/courses" class="btn btn-light btn-lg">Enroll Now</a>
            </div>
        </div>';
    }
}
