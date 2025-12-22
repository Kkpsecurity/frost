<?php

namespace App\Http\Controllers\Classroom;

use App\Http\Controllers\Controller;
use App\Services\ClassroomDashboardService;
use App\Traits\PageMetaDataTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\JsonResponse;

class ClassroomController extends Controller
{
    use PageMetaDataTrait;

    protected ClassroomDashboardService $classroomService;

    public function __construct(ClassroomDashboardService $classroomService = null)
    {
        $this->middleware('auth');
        $this->classroomService = $classroomService;
    }

    /**
     * Debug endpoint for classroom data only
     */
    public function debugClass(): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated'
                ]);
            }

            // Use dedicated classroom service
            $service = $this->classroomService ?: new ClassroomDashboardService($user);
            $classroomData = $service->getClassroomData();

            // Add sample data if empty (for current stage)
            if ($classroomData['instructors']->isEmpty() && $classroomData['courseDates']->isEmpty()) {
                $classroomData = [
                    'instructors' => collect([
                        [
                            'id' => 67,
                            'name' => 'Dr. Sarah Johnson',
                            'email' => 'sarah.johnson@security.edu',
                            'phone' => '+1-555-0123',
                            'bio' => 'Certified security expert with 15 years experience in cybersecurity training.',
                            'certifications' => ['CISSP', 'CISM', 'CEH'],
                            'profile_image' => '/images/instructors/sarah-johnson.jpg',
                            'specialties' => ['Network Security', 'Incident Response', 'Risk Management'],
                            'rating' => 4.8,
                            'total_courses' => 45,
                            'years_experience' => 15
                        ],
                        [
                            'id' => 68,
                            'name' => 'Prof. Michael Chen',
                            'email' => 'michael.chen@security.edu',
                            'phone' => '+1-555-0124',
                            'bio' => 'Former FBI cybercrime investigator, now teaching digital forensics.',
                            'certifications' => ['GCIH', 'GCFA', 'CISSP'],
                            'profile_image' => '/images/instructors/michael-chen.jpg',
                            'specialties' => ['Digital Forensics', 'Malware Analysis', 'Threat Intelligence'],
                            'rating' => 4.9,
                            'total_courses' => 32,
                            'years_experience' => 20
                        ]
                    ]),
                    'courseDates' => collect([
                        [
                            'id' => 123,
                            'course_id' => 45,
                            'instructor_id' => 67,
                            'start_date' => '2025-09-15',
                            'end_date' => '2025-09-20',
                            'start_time' => '09:00:00',
                            'end_time' => '17:00:00',
                            'timezone' => 'America/New_York',
                            'location' => 'Online Classroom A',
                            'status' => 'active',
                            'max_students' => 25,
                            'current_enrollment' => 18,
                            'meeting_link' => 'https://zoom.us/j/123456789',
                            'course_title' => 'Advanced Network Security',
                            'created_at' => '2025-08-01T10:00:00Z',
                            'updated_at' => '2025-09-10T14:30:00Z'
                        ],
                        [
                            'id' => 124,
                            'course_id' => 46,
                            'instructor_id' => 68,
                            'start_date' => '2025-10-01',
                            'end_date' => '2025-10-05',
                            'start_time' => '10:00:00',
                            'end_time' => '16:00:00',
                            'timezone' => 'America/New_York',
                            'location' => 'Digital Forensics Lab',
                            'status' => 'scheduled',
                            'max_students' => 15,
                            'current_enrollment' => 8,
                            'meeting_link' => 'https://zoom.us/j/987654321',
                            'course_title' => 'Digital Forensics Fundamentals',
                            'created_at' => '2025-08-15T14:00:00Z',
                            'updated_at' => '2025-09-10T16:00:00Z'
                        ]
                    ]),
                    'stats' => [
                        'total_instructors' => 2,
                        'total_course_dates' => 2,
                    ],
                    'data_source' => 'sample_data'
                ];
            } else {
                $classroomData['data_source'] = 'database';
            }

            return response()->json($classroomData);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get classroom dashboard data
     */
    public function dashboard(): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated'
                ], 401);
            }

            $service = $this->classroomService ?: new ClassroomDashboardService($user);
            $classroomData = $service->getClassroomData();

            return response()->json($classroomData);

        } catch (Exception $e) {
            Log::error('ClassroomController: Error getting dashboard data', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to retrieve classroom data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
