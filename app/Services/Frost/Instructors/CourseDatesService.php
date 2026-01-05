<?php

declare(strict_types=1);

namespace App\Services\Frost\Instructors;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Service for managing course dates and bulletin board data
 * Handles course statistics, upcoming courses, and bulletin board content
 */
class CourseDatesService
{
    /**
     * Get bulletin board data for instructor dashboard
     * AUTO-LOADS today's courses when activated at 7 AM via polling
     *
     * @return array
     */
    public function getBulletinBoardData(): array
    {
        // Get instructor statistics (handle different database schemas)
        try {
            $totalInstructors = DB::table('users')->where('role', 'instructor')->count();
        } catch (\Exception $e) {
            $totalInstructors = 0; // Fallback if table doesn't exist
        }

        try {
            $activeCourses = DB::table('course_dates')->where('starts_at', '>=', now()->startOfDay())
                ->where('starts_at', '<=', now()->endOfDay())->count();
        } catch (\Exception $e) {
            $activeCourses = 0; // Fallback if table doesn't exist
        }

        try {
            $totalStudents = DB::table('users')->where('status', 'active')->count();
        } catch (\Exception $e) {
            $totalStudents = 0; // Fallback if table doesn't exist
        }

        // AUTO-LOAD: Get today's actual lessons/courses (this is the key fix!)
        $todaysLessonsData = $this->getTodaysLessons();
        $todaysLessons = $todaysLessonsData['lessons'] ?? [];
        $hasLessonsToday = $todaysLessonsData['has_lessons'] ?? false;

        // Sample announcements (you can replace with actual data from announcements table)
        $announcements = [
            [
                'id' => 1,
                'title' => 'New Course Materials Available',
                'content' => 'Updated training materials for Cybersecurity Fundamentals are now available in the resources section.',
                'type' => 'training',
                'author' => 'Training Department',
                'created_at' => now()->subDays(1)->format('c'),
                'expires_at' => now()->addDays(30)->format('c')
            ],
            [
                'id' => 2,
                'title' => 'System Maintenance Scheduled',
                'content' => 'The learning management system will undergo maintenance this weekend from 10 PM to 2 AM.',
                'type' => 'urgent',
                'author' => 'IT Department',
                'created_at' => now()->subHours(6)->format('c'),
                'expires_at' => now()->addDays(3)->format('c')
            ],
            [
                'id' => 3,
                'title' => 'Updated Teaching Guidelines',
                'content' => 'Please review the updated teaching guidelines for hybrid learning environments.',
                'type' => 'policy',
                'author' => 'Academic Affairs',
                'created_at' => now()->subDays(3)->format('c'),
                'expires_at' => null
            ]
        ];

        // Add today's course announcement if courses are available
        if ($hasLessonsToday && count($todaysLessons) > 0) {
            // Insert today's courses announcement at the top
            array_unshift($announcements, [
                'id' => 'todays_courses',
                'title' => 'Today\'s Scheduled Courses (' . count($todaysLessons) . ')',
                'content' => 'Courses are now active and ready for instruction. View the course cards below to start or manage classes.',
                'type' => 'courses',
                'author' => 'System',
                'created_at' => now()->format('c'),
                'expires_at' => now()->endOfDay()->format('c')
            ]);
        }

        // Sample instructor resources (you can replace with actual data)
        $instructorResources = [
            [
                'id' => 1,
                'title' => 'Instructor Training Videos',
                'description' => 'Comprehensive video guides for effective online teaching',
                'type' => 'video',
                'category' => 'Training',
                'url' => '/instructor/resources/videos'
            ],
            [
                'id' => 2,
                'title' => 'Course Planning Template',
                'description' => 'Standardized template for lesson planning and course structure',
                'type' => 'document',
                'category' => 'Templates',
                'url' => '/instructor/resources/templates/course-planning.pdf'
            ],
            [
                'id' => 3,
                'title' => 'Student Engagement Strategies',
                'description' => 'Best practices for keeping students engaged in virtual classrooms',
                'type' => 'training',
                'category' => 'Best Practices',
                'url' => '/instructor/resources/engagement-strategies'
            ],
            [
                'id' => 4,
                'title' => 'Technical Support Portal',
                'description' => 'Quick access to technical support and troubleshooting guides',
                'type' => 'link',
                'category' => 'Support',
                'url' => '/support/technical'
            ]
        ];

        // Available courses (handle different database schemas)
        $availableCourses = [];
        try {
            $availableCourses = DB::table('courses')
                ->select(
                    'id',
                    'title',
                    'description',
                    'duration_hours',
                    'price',
                    'is_active'
                )
                ->where('is_active', 1)
                ->limit(6)
                ->get()
                ->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'title' => $course->title ?? 'Unknown Course',
                        'description' => $course->description ?? 'No description available',
                        'total_minutes' => ($course->duration_hours ?? 1) * 60, // Convert hours to minutes
                        'price' => $course->price ?? 0,
                        'is_active' => (bool) $course->is_active
                    ];
                })->toArray();
        } catch (\Exception $e) {
            // Fallback to empty array if courses table has different schema
            $availableCourses = [];
        }

        return [
            // PRIMARY FEATURE: Today's actual courses (auto-loaded at 7 AM)
            'todays_lessons' => $todaysLessons,
            'has_lessons_today' => $hasLessonsToday,
            'lessons_message' => $todaysLessonsData['message'] ?? 'No message',

            // EXISTING FEATURES: Keep current functionality
            'announcements' => $announcements,
            'available_courses' => $availableCourses,
            'instructor_resources' => $instructorResources,
            'quick_stats' => [
                'total_instructors' => $totalInstructors,
                'active_courses_today' => $activeCourses,
                'students_in_system' => $totalStudents
            ]
        ];
    }    /**
         * Get course date statistics
         *
         * @param \Illuminate\Database\Query\Builder $courseDates
         * @return array
         */
    private function getCourseDateStatistics($courseDates): array
    {
        $totalCourseDates = $courseDates->count();
        $activeCourseDates = $courseDates->where('is_active', true)->count();
        $upcomingCourseDates = $courseDates->where('starts_at', '>', now())->where('is_active', true)->count();

        $thisWeekCourseDates = $courseDates->whereBetween('starts_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->where('is_active', true)->count();

        $thisMonthCourseDates = $courseDates->whereBetween('starts_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])->where('is_active', true)->count();

        return [
            'total_course_dates' => $totalCourseDates,
            'active_course_dates' => $activeCourseDates,
            'upcoming_course_dates' => $upcomingCourseDates,
            'this_week_course_dates' => $thisWeekCourseDates,
            'this_month_course_dates' => $thisMonthCourseDates
        ];
    }

    /**
     * Get upcoming courses for bulletin board
     *
     * @param \Illuminate\Database\Query\Builder $courseDates
     * @return array
     */
    private function getUpcomingCourses($courseDates): array
    {
        $upcomingCourses = $courseDates->where('starts_at', '>', now())
            ->where('is_active', true)
            ->orderBy('starts_at', 'asc')
            ->limit(5)
            ->get();

        return $upcomingCourses->map(function ($courseDate) {
            return [
                'id' => $courseDate->id,
                'course_unit_id' => $courseDate->course_unit_id,
                'starts_at' => $courseDate->starts_at,
                'ends_at' => $courseDate->ends_at,
                'formatted_date' => Carbon::parse($courseDate->starts_at)->format('M j, Y g:i A')
            ];
        })->toArray();
    }

    /**
     * Get recent course history
     *
     * @param \Illuminate\Database\Query\Builder $courseDates
     * @return array
     */
    private function getRecentCourseHistory($courseDates): array
    {
        $recentCourseDates = $courseDates->where('starts_at', '<', now())
            ->orderBy('starts_at', 'desc')
            ->limit(5)
            ->get();

        return $recentCourseDates->map(function ($courseDate) {
            return [
                'id' => $courseDate->id,
                'course_unit_id' => $courseDate->course_unit_id,
                'starts_at' => $courseDate->starts_at,
                'ends_at' => $courseDate->ends_at,
                'formatted_date' => Carbon::parse($courseDate->starts_at)->format('M j, Y g:i A')
            ];
        })->toArray();
    }

    /**
     * Get chart data for visualization
     *
     * @param array $stats
     * @return array
     */
    private function getChartData(array $stats): array
    {
        return [
            'course_activity' => [
                'labels' => ['Total', 'Active', 'Upcoming', 'This Week', 'This Month'],
                'data' => [
                    $stats['total_course_dates'],
                    $stats['active_course_dates'],
                    $stats['upcoming_course_dates'],
                    $stats['this_week_course_dates'],
                    $stats['this_month_course_dates']
                ]
            ]
        ];
    }

    /**
     * Get today's lessons for instructor dashboard
     *
     * @return array
     */
    public function getTodaysLessons(): array
    {
        $tz = 'America/New_York';
        $todayEt = Carbon::now($tz)->startOfDay();
        $today = $todayEt->toDateString();
        $startUtc = $todayEt->copy()->tz('UTC');
        $endUtc = $todayEt->copy()->endOfDay()->tz('UTC');

        try {
            // Query today's ACTIVE CourseDate records
            $todaysCourseDates = \App\Models\CourseDate::query()
                ->where('is_active', true)
                ->whereBetween('starts_at', [$startUtc, $endUtc])
                ->with(['courseUnit.course', 'instUnit.createdBy', 'instUnit.assistant'])
                ->orderBy('starts_at')
                ->get();
        } catch (\Exception $e) {
            Log::error('CourseDatesService: Error fetching course dates', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'lessons' => [],
                'message' => "Error loading courses for today ({$today}): " . $e->getMessage(),
                'has_lessons' => false,
                'assignment_history' => [],
                'metadata' => [
                    'date' => $today,
                    'timezone' => $tz,
                    'window_utc' => [
                        'start' => $startUtc->toIso8601String(),
                        'end' => $endUtc->toIso8601String(),
                    ],
                    'count' => 0,
                    'generated_at' => now()->format('c'),
                    'error' => true
                ]
            ];
        }

        if ($todaysCourseDates->isEmpty()) {
            return [
                'lessons' => [],
                'message' => "No courses scheduled for today ({$today})",
                'has_lessons' => false,
                'assignment_history' => $this->getCourseDateAssignmentHistory(),
                'metadata' => [
                    'date' => $today,
                    'timezone' => $tz,
                    'window_utc' => [
                        'start' => $startUtc->toIso8601String(),
                        'end' => $endUtc->toIso8601String(),
                    ],
                    'count' => 0,
                    'generated_at' => now()->format('c')
                ]
            ];
        }

        try {
            $formattedLessons = $todaysCourseDates->map(function ($courseDate) {
                try {
                    // Use existing Classes instead of creating new queries
                    $courseUnit = $courseDate->GetCourseUnit();
                    $course = $courseDate->GetCourse();
                    $instUnit = $courseDate->InstUnit;

                    // Use CourseUnitObj to get lesson count properly
                    $courseUnitObj = new \App\Classes\CourseUnitObj($courseUnit);
                    $lessonCount = $courseUnitObj->CourseUnitLessons()->count();

                    // Use existing ClassroomQueries for student count
                    $studentCount = 0;
                    if ($instUnit && !$instUnit->completed_at) {
                        // Class has started but not completed - use existing ActiveStudentUnits method
                        $studentCount = \App\Classes\ClassroomQueries::ActiveStudentUnits($courseDate)->count();
                    }
                    // If no InstUnit exists, class hasn't started, so student_count = 0

                    // DEBUG: Log what we found for this course date
                    Log::info('CourseDatesService: Using model relationships', [
                        'course_date_id' => $courseDate->id,
                        'course_name' => $course->title ?? 'Unknown Course',
                        'start_time' => $courseDate->starts_at,
                        'found_inst_unit' => $instUnit !== null,
                        'inst_unit_id' => $instUnit?->id,
                        'inst_unit_created_by' => $instUnit?->created_by,
                        'inst_unit_completed_at' => $instUnit?->completed_at,
                    ]);

                    // Get instructor and assistant info using model relationships
                    $instructor = null;
                    $assistant = null;
                    if ($instUnit) {
                        $instructor = $instUnit->GetCreatedBy();
                        if ($instUnit->assistant_id) {
                            $assistant = $instUnit->GetAssistant();
                        }
                    }

                    // Get current authenticated user ID for instructor/assistant logic
                    $currentUserId = auth('admin')->id();

                    // Determine class status based on InstUnit assignment FIRST, then time
                    $now = now();
                    $startTime = Carbon::parse($courseDate->starts_at);
                    $endTime = Carbon::parse($courseDate->ends_at);

                    $classStatus = 'scheduled'; // Default: CourseDate exists but no live class
                    $buttons = [];

                    // PRIMARY LOGIC: Check InstUnit state first
                    if ($instUnit) {
                        // Log InstUnit details for debugging
                        Log::info('CourseDatesService: InstUnit found', [
                            'course_date_id' => $courseDate->id,
                            'inst_unit_id' => $instUnit->id,
                            'created_by' => $instUnit->created_by,
                            'completed_at' => $instUnit->completed_at,
                            'instructor_name' => $instructor ? ($instructor->fname ?? '') . ' ' . ($instructor->lname ?? '') : 'NO_INSTRUCTOR_FOUND'
                        ]);

                        if ($instUnit->completed_at) {
                            // Check if completed_at is actually for TODAY'S class
                            $completedAt = Carbon::parse($instUnit->completed_at);
                            $courseDateDay = Carbon::parse($courseDate->starts_at)->format('Y-m-d');
                            $completedDay = $completedAt->format('Y-m-d');

                            if ($completedDay === $courseDateDay) {
                                // InstUnit was completed TODAY - class actually finished
                                $classStatus = 'completed';
                                $buttons = ['info' => 'Class completed at ' . $completedAt->format('g:i A')];
                                Log::info('CourseDatesService: Course marked as completed (same day)', [
                                    'course_date_id' => $courseDate->id,
                                    'completed_at' => $instUnit->completed_at,
                                    'buttons' => $buttons
                                ]);
                            } else {
                                // InstUnit completed on DIFFERENT day - treat as if no InstUnit exists
                                Log::warning('CourseDatesService: InstUnit completed on different day', [
                                    'course_date_id' => $courseDate->id,
                                    'course_date_day' => $courseDateDay,
                                    'completed_day' => $completedDay,
                                    'completed_at' => $instUnit->completed_at
                                ]);

                                // Clear instructor data for stale InstUnit
                                $instructor = null;
                                $assistant = null;
                                $instUnit = null;

                                // Treat as unassigned since this is stale data
                                if ($now->lt($startTime)) {
                                    $classStatus = 'scheduled';
                                    $buttons = ['info' => 'Class starts at ' . $startTime->format('g:i A')];
                                } elseif ($now->between($startTime, $endTime) || $now->between($startTime->copy()->subHours(1), $endTime->copy()->addHours(1))) {
                                    $classStatus = 'unassigned';
                                    $buttons = ['start_class' => 'Start Class'];
                                } else {
                                    $classStatus = 'expired';
                                    $buttons = ['info' => 'Class time has ended'];
                                }
                            }
                        } else {
                            // InstUnit exists but not completed - check if it's from the same day first
                            $instUnitCreatedDay = Carbon::parse($instUnit->created_at)->format('Y-m-d');
                            $courseDateDay = Carbon::parse($courseDate->starts_at)->format('Y-m-d');

                            if ($instUnitCreatedDay !== $courseDateDay) {
                                // Stale InstUnit from different day - treat as unassigned
                                Log::warning('CourseDatesService: Uncompleted InstUnit from different day', [
                                    'course_date_id' => $courseDate->id,
                                    'course_date_day' => $courseDateDay,
                                    'inst_unit_created_day' => $instUnitCreatedDay,
                                    'created_at' => $instUnit->created_at
                                ]);

                                // Clear instructor data for stale InstUnit
                                $instructor = null;
                                $assistant = null;
                                $instUnit = null;

                                // Treat as unassigned
                                if ($now->lt($startTime)) {
                                    $classStatus = 'scheduled';
                                    $buttons = ['info' => 'Class starts at ' . $startTime->format('g:i A')];
                                } elseif ($now->between($startTime, $endTime) || $now->between($startTime->copy()->subHours(1), $endTime->copy()->addHours(1))) {
                                    $classStatus = 'unassigned';
                                    $buttons = ['start_class' => 'Start Class'];
                                } else {
                                    $classStatus = 'expired';
                                    $buttons = ['info' => 'Class time has ended'];
                                }
                            } else {
                                // InstUnit exists but not completed - check if class time has started
                                if ($now->gte($startTime)) {
                                    // Class time has started/passed and instructor is assigned
                                    $classStatus = 'in_progress';

                                    // Check if current user is the assigned instructor
                                    if ($currentUserId && $instUnit->created_by == $currentUserId) {
                                        // Current user IS the assigned instructor
                                        $buttons = [
                                            'take_control' => 'Take Control',
                                            'complete' => 'Complete Class'
                                        ];
                                    } else {
                                        // Current user is NOT the assigned instructor - show assistant view
                                        $buttons = [
                                            'assist' => 'Join as Assistant'
                                        ];
                                    }
                                } else {
                                    // Before class time but instructor assigned
                                    $classStatus = 'assigned';

                                    if ($currentUserId && $instUnit->created_by == $currentUserId) {
                                        // Current user IS the assigned instructor
                                        $buttons = ['info' => 'You are assigned - starts at ' . $startTime->format('g:i A')];
                                    } else {
                                        // Current user is NOT the assigned instructor
                                        $buttons = ['info' => 'Instructor: ' . (($instructor->fname ?? '') . ' ' . ($instructor->lname ?? '')) . ' - starts at ' . $startTime->format('g:i A')];
                                    }
                                }
                            }
                        }
                    } else {
                        // Log that no InstUnit was found
                        Log::info('CourseDatesService: No InstUnit found', [
                            'course_date_id' => $courseDate->id,
                            'start_time' => $startTime->toDateTimeString(),
                            'end_time' => $endTime->toDateTimeString(),
                            'current_time' => $now->toDateTimeString()
                        ]);
                        // No InstUnit exists - check time to determine what actions are available
                        if ($now->lt($startTime)) {
                            // Before class time
                            $classStatus = 'scheduled';
                            $buttons = ['info' => 'Class starts at ' . $startTime->format('g:i A')];
                        } elseif ($now->between($startTime, $endTime)) {
                            // During class time and no InstUnit - class is unassigned
                            $classStatus = 'unassigned';
                            $buttons = ['start_class' => 'Start Class'];
                        } elseif ($now->between($startTime->copy()->subHours(2), $endTime->copy()->addHours(8))) {
                            // Within extended window (2 hours before, 8 hours after) - allow starting
                            $classStatus = 'unassigned';
                            $buttons = ['start_class' => 'Start Class'];
                        } else {
                            // Well outside class time and no InstUnit
                            $classStatus = 'expired';
                            $buttons = ['info' => 'Class time has ended'];
                        }
                    }

                    // Lesson count is already calculated above with CourseUnitObj

                    // Create a more descriptive course name by combining course and lesson info
                    $courseTitle = $course->title ?? 'Unknown Course';
                    $lessonTitle = $courseUnit->title ?? 'Unknown Lesson';

                    // If lesson title contains meaningful information, combine them
                    $fullCourseName = $courseTitle;
                    if ($lessonTitle && $lessonTitle !== 'Unknown Lesson' && !str_contains($courseTitle, $lessonTitle)) {
                        $fullCourseName = $courseTitle . ' - ' . $lessonTitle;
                    }

                    return [
                        'id' => $courseDate->id,
                        'time' => $startTime->format('h:i A'),
                        'duration' => $startTime->diffInHours($endTime) . ' hours',
                        'course_name' => $fullCourseName,
                        'course_code' => $course->title ?? 'N/A',
                        'lesson_name' => $courseUnit->title ?? 'Unknown Lesson',
                        'module' => $courseUnit->admin_title ?? 'Module N/A',
                        'student_count' => $studentCount,
                        'lesson_count' => $lessonCount, // Add lesson count for course card
                        'class_status' => $classStatus,
                        'buttons' => $buttons,
                        'starts_at' => $courseDate->starts_at,
                        'ends_at' => $courseDate->ends_at,
                        // Add instructor directly at top level for React component compatibility
                        'instructor_name' => $instUnit && $instructor
                            ? ($instructor->fname ?? '') . ' ' . ($instructor->lname ?? '')
                            : null,
                        'assistant_name' => $instUnit && $assistant
                            ? ($assistant->fname ?? '') . ' ' . ($assistant->lname ?? '')
                            : null,
                        'inst_unit' => $instUnit ? [
                            'id' => $instUnit->id,
                            'created_by' => $instUnit->created_by,
                            'assistant_id' => $instUnit->assistant_id,
                            'instructor' => $instructor ? ($instructor->fname ?? '') . ' ' . ($instructor->lname ?? '') : null,
                            'assistant' => $assistant ? ($assistant->fname ?? '') . ' ' . ($assistant->lname ?? '') : null,
                            'created_at' => $instUnit->created_at,
                            'completed_at' => $instUnit->completed_at
                        ] : null
                    ];
                } catch (\Exception $e) {
                    Log::error('CourseDatesService: Error processing course date', [
                        'course_date_id' => $courseDate->id ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                    return [
                        'id' => $courseDate->id ?? 0,
                        'time' => 'Error',
                        'duration' => 'Error',
                        'course_name' => 'Error loading course data',
                        'course_code' => 'N/A',
                        'lesson_name' => 'Error loading lesson data',
                        'module' => 'N/A',
                        'student_count' => 0,
                        'class_status' => 'error',
                        'buttons' => ['info' => 'Error loading data'],
                        'starts_at' => null,
                        'ends_at' => null,
                        'instructor_name' => null,
                        'assistant_name' => null,
                        'inst_unit' => null
                    ];
                }
            })->toArray();

            // Get course assignment history for the table
            $assignmentHistory = $this->getCourseDateAssignmentHistory();

            return [
                'lessons' => $formattedLessons,
                'message' => count($formattedLessons) . ' lessons scheduled for today',
                'has_lessons' => true,
                'assignment_history' => $assignmentHistory,
                'metadata' => [
                    'date' => $today,
                    'count' => count($formattedLessons),
                    'generated_at' => now()->format('c')
                ]
            ];
        } catch (\Exception $e) {
            Log::error('CourseDatesService: Critical error in getTodaysLessons', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'lessons' => [],
                'message' => 'Error loading today\'s lessons: ' . $e->getMessage(),
                'has_lessons' => false,
                'assignment_history' => [],
                'metadata' => [
                    'date' => $today,
                    'count' => 0,
                    'generated_at' => now()->format('c'),
                    'error' => true
                ]
            ];
        }
    }

    /**
     * Get course dates for a specific date range
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getCourseDatesInRange(Carbon $startDate, Carbon $endDate): array
    {
        $courseDates = DB::table('course_dates')
            ->whereBetween('starts_at', [$startDate, $endDate])
            ->where('is_active', true)
            ->orderBy('starts_at', 'asc')
            ->get();

        return $courseDates->map(function ($courseDate) {
            return [
                'id' => $courseDate->id,
                'course_unit_id' => $courseDate->course_unit_id,
                'starts_at' => $courseDate->starts_at,
                'ends_at' => $courseDate->ends_at,
                'formatted_date' => Carbon::parse($courseDate->starts_at)->format('M j, Y g:i A'),
                'duration_hours' => Carbon::parse($courseDate->starts_at)->diffInHours(Carbon::parse($courseDate->ends_at))
            ];
        })->toArray();
    }

    /**
     * Get course date assignment history for the table
     * Shows all course dates with their InstUnit assignment status
     *
     * @return array
     */
    public function getCourseDateAssignmentHistory(): array
    {
        // Get all course dates from the past 30 days with their InstUnit status
        $history = DB::table('course_dates')
            ->leftJoin('inst_unit', 'course_dates.id', '=', 'inst_unit.course_date_id')
            ->leftJoin('course_units', 'course_dates.course_unit_id', '=', 'course_units.id')
            ->leftJoin('courses', 'course_units.course_id', '=', 'courses.id')
            ->leftJoin('users as instructor', 'inst_unit.created_by', '=', 'instructor.id')
            ->leftJoin('users as assistant', 'inst_unit.assistant_id', '=', 'assistant.id')
            ->where('course_dates.starts_at', '>=', now()->subDays(30))
            ->where('course_dates.is_active', true)
            ->orderBy('course_dates.starts_at', 'desc')
            ->select([
                'course_dates.id as course_date_id',
                'course_dates.starts_at',
                'course_dates.ends_at',
                'courses.title as course_name',
                'course_units.title as unit_name',
                'course_units.admin_title as unit_code',
                'course_units.ordering as day_number',
                'inst_unit.id as inst_unit_id',
                'inst_unit.created_at as assigned_at',
                'inst_unit.completed_at',
                'instructor.fname as instructor_fname',
                'instructor.lname as instructor_lname',
                'assistant.fname as assistant_fname',
                'assistant.lname as assistant_lname'
            ])
            ->get();

        return $history->map(function ($record) {
            $startTime = Carbon::parse($record->starts_at);
            $endTime = Carbon::parse($record->ends_at);

            // Determine assignment status
            $assignmentStatus = 'unassigned';
            $statusColor = 'warning';

            if ($record->inst_unit_id) {
                if ($record->completed_at) {
                    $assignmentStatus = 'completed';
                    $statusColor = 'success';
                } else {
                    $assignmentStatus = 'assigned';
                    $statusColor = 'primary';
                }
            }

            return [
                'course_date_id' => $record->course_date_id,
                'date' => $startTime->format('M j, Y'),
                'time' => $startTime->format('g:i A') . ' - ' . $endTime->format('g:i A'),
                'course_name' => $record->course_name,
                'unit_name' => $record->unit_name,
                'unit_code' => $record->unit_code,
                'day_number' => 'Day ' . $record->day_number,
                'assignment_status' => $assignmentStatus,
                'status_color' => $statusColor,
                'instructor' => $record->instructor_fname && $record->instructor_lname
                    ? $record->instructor_fname . ' ' . $record->instructor_lname
                    : null,
                'assistant' => $record->assistant_fname && $record->assistant_lname
                    ? $record->assistant_fname . ' ' . $record->assistant_lname
                    : null,
                'assigned_at' => $record->assigned_at
                    ? Carbon::parse($record->assigned_at)->format('M j, g:i A')
                    : null,
                'completed_at' => $record->completed_at
                    ? Carbon::parse($record->completed_at)->format('M j, g:i A')
                    : null,
                'duration' => $startTime->diffInHours($endTime) . 'h',
                'inst_unit_id' => $record->inst_unit_id
            ];
        })->toArray();
    }
}
