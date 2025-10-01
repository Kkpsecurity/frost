<?php

namespace App\Http\Controllers\Web;

use App\Models\Course;
use App\Traits\PageMetaDataTrait;
use App\Http\Controllers\Controller;

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
     * Display the courses list for purchase
     */
    public function list()
    {
        // Fetch active courses from database
        $courses = Course::where('is_active', true)
                        ->orderBy('title')
                        ->get();

        $content = [
            'title' => 'Course List - ' . config('app.name'),
            'description' => 'Browse and purchase our professional security training courses',
            'keywords' => 'security courses, buy courses, training programs, certification courses',
        ];

        // Merge with meta data
        $content = array_merge($content, self::renderPageMeta('courses-list'));

        return view('frontend.courses.list', compact('content', 'courses'));
    }

    /**
     * Display a specific course
     */
    public function show(Course $course)
    {
        // Check if course is active
        if (!$course->is_active) {
            abort(404, 'Course not found');
        }

        // Get course units and lessons from database
        $courseUnits = $course->GetCourseUnits();
        $courseLessons = $course->GetLessons();

        // Transform lessons into features list
        $features = [];
        if ($courseLessons->count() > 0) {
            // Use actual lesson titles as features
            foreach ($courseLessons->take(10) as $lesson) { // Limit to 10 for display
                $features[] = $lesson->title;
            }
        }

        // If no lessons, use course description or basic info
        if (empty($features)) {
            $features = [];
            if ($course->description) {
                $features[] = 'Professional Security Training';
                $features[] = 'State-Approved Curriculum';
                $features[] = 'Expert Instruction';
                $features[] = 'Industry Certification';
            }
        }

        // Add curriculum content descriptions for each course unit
        $courseUnitsWithContent = $courseUnits->map(function ($unit) use ($course) {
            $unit->curriculum_content = $this->getCurriculumContentForUnit($unit, $course);
            return $unit;
        });

        // Transform Course model data to match the view expectations
        $courseData = [
            'id' => $course->id,
            'title' => $course->title_long ?? $course->title,
            'type' => $course->type ?? null,
            'badge' => $this->getCourseBadgeFromTitle($course->title),
            'description' => $course->description ?? null,
            'fullDescription' => $course->description ?? null,
            'price' => $course->price,
            'duration' => $course->total_minutes ? ceil($course->total_minutes / 60) . ' Hours' : null,
            'format' => $course->format ?? null,
            'level' => $course->level ?? null,
            'language' => $course->language ?? null,
            'certification' => $course->certification ?? 'State Approved',
            'classSize' => $course->class_size ?? null,
            'studentsEnrolled' => $course->students_enrolled ?? null,
            'icon' => $this->getCourseIconFromTitle($course->title),
            'features' => $features, // Use real lesson data
            'courseUnits' => $courseUnitsWithContent, // Add course units with curriculum content for display
            'requirements' => $this->getCourseRequirementsFromTitle($course->title),
            'popular' => $course->is_popular ?? false,
            'keywords' => $course->keywords ?? 'security training, certification, professional development'
        ];

        $content = [
            'title' => $courseData['title'] . ' - ' . config('app.name'),
            'description' => $courseData['description'],
            'keywords' => $courseData['keywords'],
        ];

        // Merge with meta data
        $content = array_merge($content, self::renderPageMeta('course-details'));

        return view('frontend.courses.show', compact('content'), ['course' => $courseData]);
    }

    /**
     * Display the course enrollment page
     */
    public function enroll(Course $course)
    {
        // Check if course is active
        if (!$course->is_active) {
            abort(404, 'Course not found');
        }

        // Check if user is already enrolled
        if (auth()->user()->ActiveCourseAuths->firstWhere('course_id', $course->id)) {
            return redirect()->route('courses.show', $course->id)
                           ->with('warning', 'You are already enrolled in this course.');
        }

        // Get course data similar to show method but focused on enrollment
        $courseData = [
            'id' => $course->id,
            'title' => $course->title_long ?? $course->title,
            'badge' => $this->getCourseBadgeFromTitle($course->title),
            'description' => $this->getCourseDescriptionFromTitle($course->title),
            'price' => $course->price,
            'duration' => $course->total_minutes ? ceil($course->total_minutes / 60 / 8) . ' Days' : $this->getDefaultDurationFromTitle($course->title),
            'icon' => $this->getCourseIconFromTitle($course->title),
            'requirements' => $this->getCourseRequirementsFromTitle($course->title),
        ];

        $content = [
            'title' => 'Enroll in ' . $courseData['title'] . ' - ' . config('app.name'),
            'description' => 'Complete your enrollment for ' . $courseData['title'],
            'keywords' => 'course enrollment, security training registration, ' . $this->getCourseKeywordsFromTitle($course->title),
        ];

        // Merge with meta data
        $content = array_merge($content, self::renderPageMeta('course-enrollment'));

        return view('frontend.courses.enroll', compact('content'), ['course' => $courseData]);
    }

    /**
     * Get course badge from title
     */
    private function getCourseBadgeFromTitle($title)
    {
        if (strpos($title, 'D40') !== false || strpos($title, "Class 'D'") !== false) {
            return 'CLASS D';
        } elseif (strpos($title, 'G28') !== false || strpos($title, "Class 'G'") !== false) {
            return 'CLASS G';
        }
        return 'SECURITY COURSE';
    }

    /**
     * Get course description from title
     */
    private function getCourseDescriptionFromTitle($title)
    {
        if (strpos($title, 'D40') !== false || strpos($title, "Class 'D'") !== false) {
            return 'Comprehensive training for unarmed security professionals. This course covers legal requirements, professional conduct, observation techniques, and report writing for unarmed security officers.';
        } elseif (strpos($title, 'G28') !== false || strpos($title, "Class 'G'") !== false) {
            return 'Essential training for armed security professionals with firearms certification. Learn firearms safety, legal requirements, use of force protocols, and professional responsibilities for armed security officers.';
        }
        return 'Professional security training program designed to prepare you for a successful career in the security industry.';
    }

    /**
     * Get course full description from title
     */
    private function getCourseFullDescriptionFromTitle($title)
    {
        if (strpos($title, 'D40') !== false || strpos($title, "Class 'D'") !== false) {
            return '<p>This comprehensive Class D training program provides essential skills for unarmed security professionals. Our experienced instructors combine theoretical knowledge with practical application to ensure you\'re prepared for real-world scenarios.</p><p>You\'ll learn professional observation techniques, effective report writing, legal boundaries, and professional conduct standards. The course emphasizes communication skills, emergency procedures, and de-escalation techniques critical for success in the security field.</p><p>Upon completion, you\'ll receive state certification qualifying you to work as an unarmed security officer in Florida and reciprocal states.</p>';
        } elseif (strpos($title, 'G28') !== false || strpos($title, "Class 'G'") !== false) {
            return '<p>This comprehensive Class G training program is designed to prepare you for a successful career as an armed security professional. Our expert instructors bring decades of real-world experience in law enforcement and private security.</p><p>The course combines theoretical knowledge with practical application, ensuring you understand not only what to do, but why and how to do it safely and legally. You\'ll learn critical skills including firearms safety, legal boundaries, crisis management, and professional communication.</p><p>Upon successful completion, you\'ll receive a state-approved certificate that qualifies you to work as an armed security officer in Florida and many other states with reciprocal agreements.</p>';
        }
        return '<p>This professional security training program provides comprehensive education in security fundamentals, legal requirements, and practical skills needed for success in the security industry.</p>';
    }

    /**
     * Get default duration from title
     */
    private function getDefaultDurationFromTitle($title)
    {
        if (strpos($title, 'D40') !== false || strpos($title, '40 Hour') !== false) {
            if (strpos($title, '5 Days') !== false || strpos($title, 'Dy') !== false) {
                return '5 Days';
            } elseif (strpos($title, '10 Nights') !== false || strpos($title, 'Nt') !== false) {
                return '10 Nights';
            }
            return '5 Days';
        } elseif (strpos($title, 'G28') !== false || strpos($title, '28 Hour') !== false) {
            return '3 Days';
        }
        return '3-5 Days';
    }

    /**
     * Get course icon from title
     */
    private function getCourseIconFromTitle($title)
    {
        if (strpos($title, 'G28') !== false || strpos($title, "Class 'G'") !== false) {
            return 'fas fa-shield-alt'; // Armed security icon
        }
        return 'fas fa-user-shield'; // Unarmed security icon
    }

    /**
     * Get course requirements from title
     */
    private function getCourseRequirementsFromTitle($title)
    {
        if (strpos($title, 'G28') !== false || strpos($title, "Class 'G'") !== false) {
            return [
                'Must be 21 years or older for armed security',
                'Valid government-issued photo ID required',
                'Pass comprehensive background check',
                'Physical and mental fitness requirements',
                'High school diploma or equivalent',
                'Clean criminal record required',
                'Pass psychological evaluation',
                'Complete firearms safety course'
            ];
        } else {
            return [
                'Must be 18 years or older',
                'Valid government-issued photo ID required',
                'Pass background check',
                'High school diploma or equivalent',
                'Good physical condition',
                'Clean criminal record preferred'
            ];
        }
    }

    /**
     * Get course keywords from title
     */
    private function getCourseKeywordsFromTitle($title)
    {
        if (strpos($title, 'G28') !== false || strpos($title, "Class 'G'") !== false) {
            return 'armed security, Class G, firearms training';
        } else {
            return 'unarmed security, Class D, surveillance training';
        }
    }

    /**
     * Get key features for course display
     */
    private function getCourseKeyFeatures($title)
    {
        if (strpos($title, 'G28') !== false || strpos($title, "Class 'G'") !== false) {
            return [
                'Firearms Training & Certification',
                'Legal Requirements & Regulations',
                'Use of Force Protocols',
                'Professional Responsibilities',
                'State Exam Preparation',
                'Certificate Upon Completion'
            ];
        } else {
            return [
                'Surveillance Techniques',
                'Professional Report Writing',
                'Legal Boundaries & Ethics',
                'Communication Skills',
                'Emergency Procedures',
                'State Certification'
            ];
        }
    }

    /**
     * Generate curriculum content description for a course unit
     */
    private function getCurriculumContentForUnit($unit, $course)
    {
        // Get the unit's admin title to determine content type
        $adminTitle = $unit->admin_title ?? '';
        $unitTitle = $unit->title ?? '';
        $courseTitle = $course->title ?? '';

        // Generate content based on course title and unit
        if (strpos($courseTitle, 'G28') !== false || strpos($courseTitle, "Class 'G'") !== false) {
            // Class G (Armed Security) curriculum content
            if (strpos($adminTitle, 'G28-D1') !== false || strpos($unitTitle, 'Day 1') !== false) {
                return 'Legal aspects of private security, officer roles, and firearms safety fundamentals for armed security personnel.';
            } elseif (strpos($adminTitle, 'G28-D2') !== false || strpos($unitTitle, 'Day 2') !== false) {
                return 'Advanced firearms training, use of force principles, and tactical decision-making in armed security situations.';
            } elseif (strpos($adminTitle, 'G28-D3') !== false || strpos($unitTitle, 'Day 3') !== false) {
                return 'Marksmanship qualification, weapon maintenance, and final certification preparation for armed security professionals.';
            }
        } else {
            // Class D (Unarmed Security) curriculum content
            if (strpos($adminTitle, 'D1') !== false || strpos($unitTitle, 'Day 1') !== false || strpos($unitTitle, 'Night 1') !== false) {
                return 'Introduction to unarmed security fundamentals, legal requirements, and professional conduct standards.';
            } elseif (strpos($adminTitle, 'D2') !== false || strpos($unitTitle, 'Day 2') !== false || strpos($unitTitle, 'Night 2') !== false) {
                return 'Security officer conduct, communication systems, and professional responsibility standards in civilian environments.';
            } elseif (strpos($adminTitle, 'D3') !== false || strpos($unitTitle, 'Day 3') !== false || strpos($unitTitle, 'Night 3') !== false) {
                return 'Observation techniques, incident reporting, and emergency preparedness procedures for unarmed security personnel.';
            } elseif (strpos($adminTitle, 'D4') !== false || strpos($unitTitle, 'Day 4') !== false || strpos($unitTitle, 'Night 4') !== false) {
                return 'Advanced patrolling techniques, interviewing skills, and physical security assessment methodologies for professionals.';
            } elseif (strpos($adminTitle, 'D5') !== false || strpos($unitTitle, 'Day 5') !== false || strpos($unitTitle, 'Night 5') !== false) {
                return 'Safety awareness, crisis management, and final examination preparation for unarmed security certification.';
            } elseif (strpos($adminTitle, 'N6') !== false || strpos($unitTitle, 'Night 6') !== false) {
                return 'Access control systems, safeguarding information, and terrorism awareness for security personnel.';
            } elseif (strpos($adminTitle, 'N7') !== false || strpos($unitTitle, 'Night 7') !== false) {
                return 'Physical security principles, event security, and special assignments for professional security officers.';
            } elseif (strpos($adminTitle, 'N8') !== false || strpos($unitTitle, 'Night 8') !== false) {
                return 'Medical emergency response, first aid procedures, and crisis management protocols for security officers.';
            } elseif (strpos($adminTitle, 'N9') !== false || strpos($unitTitle, 'Night 9') !== false) {
                return 'Special issues in security, professional development, and advanced de-escalation techniques for officers.';
            } elseif (strpos($adminTitle, 'N10') !== false || strpos($unitTitle, 'Night 10') !== false) {
                return 'Final review, comprehensive examination preparation, and career development guidance for security professionals.';
            }
        }

        // Default content if no specific match
        return 'Comprehensive training module covering essential security concepts and practical application of professional standards.';
    }
}
