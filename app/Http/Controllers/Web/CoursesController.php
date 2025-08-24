<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Traits\PageMetaDataTrait;

class CoursesController extends Controller
{
    use PageMetaDataTrait;

    /**
     * Display the courses listing page
     */
    public function index()
    {
        $content = [
            'title' => 'Security Training Courses - ' . config('app.name'),
            'description' => 'Professional security training courses for armed and unarmed security professionals',
            'keywords' => 'security courses, training, certification, armed security, unarmed security, Class D, Class G',
        ];

        // Merge with meta data
        $content = array_merge($content, self::renderPageMeta('courses'));

        return view('frontend.courses.index', compact('content'));
    }

    /**
     * Display a specific course
     */
    public function show($slug)
    {
        // Course data mapping - In a real application, this would come from a database
        $courses = $this->getCourseData();

        $course = collect($courses)->firstWhere('slug', $slug);

        if (!$course) {
            abort(404, 'Course not found');
        }

        $content = [
            'title' => $course['title'] . ' - ' . config('app.name'),
            'description' => $course['description'],
            'keywords' => $course['keywords'] ?? 'security training, certification, professional development',
        ];

        // Merge with meta data
        $content = array_merge($content, self::renderPageMeta('course-details'));

        return view('frontend.courses.show', compact('content', 'course'));
    }

    /**
     * Get course data - In production, this would be from database
     */
    private function getCourseData()
    {
        return [
            [
                'id' => 1,
                'slug' => 'class-d-security',
                'title' => 'Class D Course - Armed Security License',
                'type' => 'Armed Security Training',
                'badge' => 'CLASS D',
                'description' => 'Comprehensive training for armed security professionals with firearms certification. This course covers firearms safety, legal requirements, use of force protocols, and professional responsibilities for armed security officers.',
                'fullDescription' => '<p>This comprehensive Class D training program is designed to prepare you for a successful career as an armed security professional. Our expert instructors bring decades of real-world experience in law enforcement and private security.</p>
                    <p>The course combines theoretical knowledge with practical application, ensuring you understand not only what to do, but why and how to do it safely and legally. You\'ll learn critical skills including firearms safety, legal boundaries, crisis management, and professional communication.</p>
                    <p>Upon successful completion, you\'ll receive a state-approved certificate that qualifies you to work as an armed security officer in Florida and many other states with reciprocal agreements.</p>',
                'price' => 299.00,
                'duration' => '5 Days',
                'format' => 'Hybrid (Online + In-Person)',
                'level' => 'Entry Level',
                'language' => 'English',
                'certification' => 'State Approved',
                'classSize' => '12 Students Max',
                'studentsEnrolled' => '200+',
                'icon' => 'fas fa-shield-alt',
                'features' => [
                    'Firearms Training & Certification',
                    'Legal Requirements & Regulations',
                    'Use of Force Protocols',
                    'Professional Responsibilities',
                    'Crisis De-escalation Techniques',
                    'Report Writing & Documentation',
                    'Emergency Response Procedures',
                    'State Exam Preparation',
                    'Hands-on Training Exercises',
                    'Certificate Upon Completion'
                ],
                'requirements' => [
                    'Must be 21 years or older for armed security',
                    'Valid government-issued photo ID required',
                    'Pass comprehensive background check',
                    'Physical and mental fitness requirements',
                    'High school diploma or equivalent',
                    'No disqualifying criminal history'
                ],
                'schedule' => [
                    [
                        'date' => 'March 15-19, 2025',
                        'time' => '8:00 AM - 5:00 PM',
                        'location' => 'Miami Training Center',
                        'available' => true
                    ],
                    [
                        'date' => 'April 12-16, 2025',
                        'time' => '8:00 AM - 5:00 PM',
                        'location' => 'Orlando Training Center',
                        'available' => true
                    ],
                    [
                        'date' => 'May 10-14, 2025',
                        'time' => '8:00 AM - 5:00 PM',
                        'location' => 'Tampa Training Center',
                        'available' => false
                    ]
                ],
                'popular' => false,
                'detailUrl' => '/courses/class-d-security',
                'enrollUrl' => '/enroll/class-d',
                'keywords' => 'armed security, class d license, firearms training, security certification'
            ],
            [
                'id' => 2,
                'slug' => 'class-g-security',
                'title' => 'Class G Course - Unarmed Security License',
                'type' => 'Unarmed Security Training',
                'badge' => 'CLASS G',
                'description' => 'Essential training for unarmed security professionals and private investigators. Learn surveillance techniques, report writing, legal boundaries, and professional conduct standards.',
                'fullDescription' => '<p>The Class G training program provides comprehensive preparation for unarmed security professionals and private investigators. This course is perfect for those seeking to enter the security industry or enhance their existing skills.</p>
                    <p>Our curriculum covers essential topics including legal authority, surveillance techniques, emergency procedures, and professional communication. You\'ll gain practical experience through real-world scenarios and case studies.</p>
                    <p>This certification opens doors to various career opportunities in corporate security, retail loss prevention, private investigation, and facility protection roles.</p>',
                'price' => 199.00,
                'duration' => '3 Days',
                'format' => 'Hybrid (Online + In-Person)',
                'level' => 'Entry Level',
                'language' => 'English',
                'certification' => 'State Approved',
                'classSize' => '15 Students Max',
                'studentsEnrolled' => '300+',
                'icon' => 'fas fa-user-shield',
                'features' => [
                    'Surveillance Techniques',
                    'Professional Report Writing',
                    'Legal Boundaries & Ethics',
                    'Communication Skills',
                    'Emergency Procedures',
                    'Access Control Systems',
                    'Incident Management',
                    'Customer Service Excellence',
                    'State Certification Prep',
                    'Career Placement Assistance'
                ],
                'requirements' => [
                    'Must be 18 years or older',
                    'Valid government-issued photo ID required',
                    'Basic background check required',
                    'High school diploma or equivalent preferred',
                    'Ability to communicate effectively in English',
                    'Physical ability to perform security duties'
                ],
                'schedule' => [
                    [
                        'date' => 'March 8-10, 2025',
                        'time' => '9:00 AM - 5:00 PM',
                        'location' => 'Miami Training Center',
                        'available' => true
                    ],
                    [
                        'date' => 'April 5-7, 2025',
                        'time' => '9:00 AM - 5:00 PM',
                        'location' => 'Jacksonville Training Center',
                        'available' => true
                    ],
                    [
                        'date' => 'May 3-5, 2025',
                        'time' => '9:00 AM - 5:00 PM',
                        'location' => 'Fort Lauderdale Training Center',
                        'available' => true
                    ]
                ],
                'popular' => true,
                'detailUrl' => '/courses/class-g-security',
                'enrollUrl' => '/enroll/class-g',
                'keywords' => 'unarmed security, class g license, security training, private investigator'
            ]
        ];
    }
}
