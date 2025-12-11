<?php
namespace App\Http\Controllers\React;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\User;
use Firebase\JWT\JWT;
use App\Models\ExamAuth;
use App\Models\InstUnit;
use App\Models\Challenge;
use App\Models\Validation;
use App\Models\InstLesson;
use App\Classes\Challenger;
use Illuminate\Http\Request;
use App\Models\StudentLesson;
use App\Classes\TrackingQueries;
use App\Traits\PageMetaDataTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
# use App\Http\Controllers\React\Traits\SelfStudyTrait;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Classes\ChatLogCache;
use App\Classes\ClassroomQueries;
use App\Classes\PollingLog;

# use RCache;
use KKP\Laravel\PgTk;
use App\Helpers\Helpers;
use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Models\StudentUnit;
use App\Classes\CourseAuthObj;
use App\Classes\ValidationsPhotos;
use Illuminate\Support\Facades\Cache;
use KKP\Laravel\Traits\StoragePathTrait;
use Illuminate\Validation\ValidationException;

class StudentPortalController extends Controller
{
    use PageMetaDataTrait;
    use StoragePathTrait;
    # use SelfStudyTrait;

    protected $classData = [];

    public function StoragePath()
    {
        return 'validations';
    }

    /**
     * Student Dashboard
     */
    public function dashboard()
    {
        $incompleteAuths = Auth::user()->ActiveCourseAuths()->paginate(10);
        $completedAuths = Auth::user()->InActiveCourseAuths()->whereNull('disabled_at')->paginate(10);

        $content = array_merge(
            ['MergedCourseAuths' => $incompleteAuths->merge($completedAuths)],
            self::renderPageMeta('index')
        );

        return view('frontend.students.dashboard', compact('content'));
    }

    /**
     * Classroom Dashboard - Modern React Interface
     */
    public function classroom()
    {
        $content = array_merge(
            [],
            self::renderPageMeta('classroom', [
                'title' => 'Classroom Dashboard',
                'description' => 'Access your courses, assignments, and classroom resources',
                'keywords' => 'classroom, dashboard, student, courses, assignments'
            ])
        );

        return view('frontend.students.classroom', compact('content'));
    }

    /**
     * API: Get student profile for classroom
     */
    public function getProfile()
    {
        // Dummy profile data for now
        return response()->json([
            'id' => 1,
            'name' => 'Demo Student',
            'email' => 'student@example.com',
            'avatar' => null,
        ]);
    }    /**
         * API: Get comprehensive classroom data
         */
    public function getClassroomDashboardData()
    {
        // Dummy data for now - will be replaced with master endpoint responses
        return response()->json([
            'courses' => [
                [
                    'id' => 1,
                    'title' => 'Florida Class D Security License',
                    'description' => 'Comprehensive security training course',
                    'progress' => 65,
                    'instructor' => 'John Smith',
                    'status' => 'active',
                ],
                [
                    'id' => 2,
                    'title' => 'Armed Security Class G License',
                    'description' => 'Firearms and armed security training',
                    'progress' => 30,
                    'instructor' => 'Sarah Johnson',
                    'status' => 'active',
                ],
            ],
            'assignments' => [
                [
                    'id' => 1,
                    'title' => 'Legal Framework Quiz',
                    'description' => 'Quiz covering security laws and regulations',
                    'due_date' => now()->addDays(5)->toISOString(),
                    'status' => 'pending',
                    'course_id' => 1,
                ],
                [
                    'id' => 2,
                    'title' => 'Emergency Response Plan',
                    'description' => 'Create a detailed emergency response plan',
                    'due_date' => now()->addDays(10)->toISOString(),
                    'status' => 'pending',
                    'course_id' => 1,
                ],
                [
                    'id' => 3,
                    'title' => 'Firearms Safety Assessment',
                    'description' => 'Assessment of firearms safety knowledge',
                    'due_date' => now()->addDays(15)->toISOString(),
                    'status' => 'pending',
                    'course_id' => 2,
                ],
            ],
            'schedule' => [
                [
                    'id' => 1,
                    'title' => 'Security Law Fundamentals',
                    'start_time' => now()->addDays(2)->hour(10)->minute(0)->toISOString(),
                    'end_time' => now()->addDays(2)->hour(11)->minute(30)->toISOString(),
                    'course_id' => 1,
                    'type' => 'class',
                ],
                [
                    'id' => 2,
                    'title' => 'Live Fire Exercise',
                    'start_time' => now()->addDays(7)->hour(14)->minute(0)->toISOString(),
                    'end_time' => now()->addDays(7)->hour(17)->minute(0)->toISOString(),
                    'course_id' => 2,
                    'type' => 'class',
                ],
            ],
            'messages' => [
                [
                    'id' => 1,
                    'title' => 'Welcome to Your Classroom',
                    'content' => 'Welcome to your student classroom dashboard. Here you can track your progress, view assignments, and communicate with instructors.',
                    'from' => 'System Administrator',
                    'read' => false,
                    'created_at' => now()->subHour()->toISOString(),
                ],
            ],
            'stats' => [
                'activeEnrollments' => 2,
                'pendingAssignments' => 3,
                'unreadMessages' => 1,
                'overallProgress' => 47,
            ],
        ]);
    }    /**
         * API: Mark message as read
         */
    public function markMessageRead($messageId)
    {
        // Dummy response for now
        return response()->json(['success' => true, 'message' => 'Message marked as read']);
    }

    /**
     * API: Submit assignment
     */
    public function submitAssignment(Request $request, $assignmentId)
    {
        // Dummy response for now
        return response()->json(['success' => true, 'message' => 'Assignment submitted successfully']);
    }

    /**
     * API: Get schedule data
     */
    public function getSchedule()
    {
        // Dummy schedule data for now
        return response()->json([
            [
                'id' => 1,
                'title' => 'Security Law Fundamentals',
                'start_time' => now()->addDays(2)->hour(10)->minute(0)->toISOString(),
                'end_time' => now()->addDays(2)->hour(11)->minute(30)->toISOString(),
                'course_id' => 1,
                'type' => 'class',
            ],
            [
                'id' => 2,
                'title' => 'Live Fire Exercise',
                'start_time' => now()->addDays(7)->hour(14)->minute(0)->toISOString(),
                'end_time' => now()->addDays(7)->hour(17)->minute(0)->toISOString(),
                'course_id' => 2,
                'type' => 'class',
            ],
        ]);
    }


    public function RunPortal(Request $request, $course_auth_id)
    {
        // Define the cache key uniquely, considering the course_auth_id
        $cacheKey = 'portal_' . $course_auth_id;

        // Check if the content is already cached
        $content = Cache::remember($cacheKey, 15 * 60, function () use ($course_auth_id) {
            // If not cached, fetch and generate the content
            $content = array_merge([], self::renderPageMeta('index'));
            return compact('content', 'course_auth_id');
        });

        // Return the view with the cached content
        return view('frontend.students.student_portal', $content);
    }
    public function getZoomPlayer($course_auth_id, $course_date_id)
    {
        // Initialize content array with provided parameters and meta data
        $content = array_merge([
            'course_auth_id' => $course_auth_id,
            'course_date_id' => $course_date_id
        ], self::renderPageMeta('index'));

        // Extract courseAuthId and courseDateId from content
        $courseAuthId = $content['course_auth_id'] ?? null;
        $courseDateId = $content['course_date_id'] ?? null;

        // Validate IDs
        if (!$courseAuthId || !$courseDateId) {
            $content['error'] = 'Invalid course authentication or course date ID.';
            return view('frontend.students.zoomplayer2', compact('content'));
        }

        // Fetch CourseAuth record
        $courseAuth = \App\Models\CourseAuth::find($courseAuthId);
        if (!$courseAuth) {
            $content['error'] = 'Course authentication not found.';
            return view('frontend.students.zoomplayer2', compact('content'));
        }

        // Fetch Course record
        $course = \App\Models\Course::find($courseAuth->course_id);
        if (!$course) {
            $content['error'] = 'Course not found.';
            return view('frontend.students.zoomplayer2', compact('content'));
        }

        // Fetch Zoom credentials
        $zoom = $course->ZoomCreds;
        if (!$zoom) {
            $content['error'] = 'Zoom credentials not found.';
            return view('frontend.students.zoomplayer2', compact('content'));
        }

        // Decrypt Zoom credentials and handle exceptions
        try {
            $content['password'] = Crypt::decrypt($zoom->zoom_password);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            $content['password'] = ''; // Handle the exception as needed
            \Log::error('Error decrypting Zoom password', ['exception' => $e]);
        }

        try {
            $content['passcode'] = Crypt::decrypt($zoom->zoom_passcode);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            $content['passcode'] = ''; // Handle the exception as needed
            \Log::error('Error decrypting Zoom passcode', ['exception' => $e]);
        }

        // Additional data required for the view
        $content['zoomVersion'] = '2.18.2';
        $content['meetingNumber'] = $zoom->pmi;
        $content['userName'] = 'STG Instructor';
        $content['userEmail'] = $zoom->zoom_email;
        $content['clientID'] = config('zoom.api_key');
        $content['clientSecret'] = config('zoom.api_secret');

        // Return the view with the content array
        return view('frontend.students.zoomplayer2', compact('content'));
    }


    protected function setClassData($key, $val)
    {
        $this->classData[$key] = $val;
    }

    protected function getClassData($key)
    {
        return $this->classData[$key];
    }

    public function classroomData()
    {
        return $this->classData;
    }

    protected function getAssignedInstructor($CourseDate = null)
    {
        // Exit early if CourseDate is null
        if ($CourseDate === null) {
            return null;
        }

        $instructor = $CourseDate->InstUnit ? $CourseDate->InstUnit->GetCreatedBy() : null;

        // Exit early if instructor is null
        if ($instructor === null) {
            return null;
        }

        $instructor->avatar = $instructor->getAvatar('thumb');
        $instructor->zoom_payload = $CourseDate->InstUnit->GetCourse()->zoomCreds;

        // If zoom_payload is empty or zoom_passcode is empty, exit early
        if (empty($instructor->zoom_payload) || empty($instructor->zoom_payload['zoom_passcode'])) {
            return $instructor;
        }

        try {

            // Decrypt zoom_passcode once and store it in a variable
            $zoom_passcode_decrypted = Crypt::decrypt($instructor->zoom_payload['zoom_passcode']);

            // Update the zoom_payload and zoom array with decrypted data
            $instructor->zoom_payload['zoom_passcode'] = $zoom_passcode_decrypted;

            $zoom = [
                "meeting_id" => $instructor->zoom_payload['pmi'],
                'zoom_passcode' => $zoom_passcode_decrypted,
            ];
        } catch (DecryptException $e) {
            // Handle the exception, perhaps log it or set zoom_passcode to a default value
            \Log::error("Decryption failed: " . $e->getMessage());
            $instructor->zoom_payload['zoom_passcode'] = ''; // Default value
        }

        return $instructor; // Ensure the instructor object is returned at the end of the function
    }

    protected function setLiveClassData($CourseAuth, $CourseDate)
    {

        $this->setClassData('courseDate', $CourseDate);

        /**
         * Get the Students Course
         */
        $this->setClassData('course', $CourseDate->getCourse() ?? []);

        /**
         * Prepares the Instructor managing the class
         */
        $this->setClassData('instructor', $instructor = $this->getAssignedInstructor($CourseDate));

        /**
         * Get the lessons for the course
         * Note: Returns the current day lessons if the class is live
         */
        $this->setClassData('lessons', $lessons = $CourseDate->GetCourseUnit()->GetLessons()->toArray());

        /**
         * Student Unit is created when the instructor starts the class         *
         */
        if ($StudentUnit = ClassroomQueries::InitStudentUnit($CourseAuth, $CourseDate)) {
            try {

                $this->setClassData('studentUnit', $StudentUnit);
                $this->setClassData('student_unit_id', $StudentUnit->id);

                /**
                 * Student lesson
                 */
                $studentLesson = ClassroomQueries::InitStudentLesson($StudentUnit);
                $this->setClassData('studentLesson', $studentLesson);


            } catch (\Exception $e) {
                Log::error("Error retrieving student unit data: " . $e->getMessage());
            }

        } else {
            $this->setClassData('studentUnit', null);
            $this->setClassData('student_unit_id', null);
            $this->setClassData('studentLesson', null);
        }

        /**
         * Check for a live class
         * This is base off the Instructors Session
         */
        $this->setClassData('instUnit', $CourseDate->InstUnit);
        $this->setClassData('instUnitLesson', $CourseDate->InstUnit ? ClassroomQueries::ActiveInstLesson($CourseDate->InstUnit) : null);
        $isLiveClass = $CourseDate && ($CourseDate->InstUnit && is_null($CourseDate->InstUnit->completed_at));

        $studentLesson = $this->getClassData('studentLesson');
        $this->setClassData('activeLesson', $studentLesson && $studentLesson->lesson_id);
        $this->setClassData('is_live_class', $isLiveClass);

        /**
         * Check for completed lessons for the instructor Unit
         */
        $completedInstLessons = ClassroomQueries::CompletedInstLessons($CourseDate->InstUnit);
        $this->setClassData('completedInstLessons', $completedInstLessons);

        /**
         * Checks if the Live Zoom Session has been setup
         * we simply check if the pmi is set
         */
        $isZoomReady = ($instructor && $instructor->zoom_payload && $instructor->zoom_payload['zoom_status'] === 'enabled');
        $this->setClassData('isZoomReady', $isZoomReady);

        /**
         * Get the InstUnitLesson
         */
        $instUnitLesson = $CourseDate->InstUnit ? ClassroomQueries::ActiveInstLesson($CourseDate->InstUnit) : null;
        if ($instUnitLesson) {
            $instUnitLesson->created_at = date('Y-m-d H:i:s', $instUnitLesson->created_at);
        }

        $this->setClassData('lessonPaused', $instUnitLesson->is_paused ?? false);


        /**
         * Course Unit Lessons
         */
        $this->setClassData("courseUnitLessons", $CourseDate ? $CourseDate->getCourseUnit()->getCourseUnitLessons() : null);


        /**
         * Check if the student is in a lesson
         * Only if they have previous completed lessons
         */
        $lessonInProgress = false;
        if ($instUnitLesson ?? false) {
            $this->setClassData('lessonInProgress', true);
        } else {
            $this->setClassData('lessonIOnProgress', false);
        }

        /**
         * Get the lesson ids
         */
        $lessonIds = array_map(function ($lesson) {
            return $lesson['id'];
        }, $lessons);

        /**
         * Get the previous completed lessons by the student
         */
        $this->setClassData('previousCompletedLessons', array_keys($CourseAuth->PCLCache()));

        /**
         * Lessons Completed for today Course
         */
        $completedStudentLessons = StudentLesson::where('student_unit_id', $StudentUnit->id)
            ->where(function ($query) {
                $query->whereNotNull('completed_at')
                    ->orWhereNotNull('dnc_at');
            })
            ->get()
            ->toArray();

        /**
         * Get the lesson ids
         */
        $completedLessonIds = array_map(function ($lesson) {
            return $lesson['id'];
        }, $completedStudentLessons);

        /**
         * Get the lessons for the course
         * Note: This is used for the Naviagtion
         */
        $this->setClassData('courseLessons', $lessons);

        /**
         * Find lessons that are in both the course's lessons and
         * previously completed lessons
         */
        foreach ($lessonIds as $lessonId) {
            if (in_array($lessonId, $this->getClassData('previousCompletedLessons'))) {
                $completedStudentLessons[] = $lessonId;
            }
        }

        $this->setClassData('previousLessons', is_array($completedStudentLessons) ? $completedStudentLessons : []);

        /**
         * Get the total number of lessons for the course
         * Get the total number of completed lessons for the student
         */
        $allCompletedStudentLessonsTotal = count($completedStudentLessons) + count($this->getClassData('previousCompletedLessons'));
        $this->setClassData('allCompletedStudentLessonsTotal', $allCompletedStudentLessonsTotal);
        $allLessonsTotal = $CourseDate->GetCourse()->GetLessons()->count();
        $this->setClassData('allLessonsTotal', $allLessonsTotal ?? 0);


        /**
         * PollingLog
         */
        if ( $studentLesson )
        {
            ( new PollingLog( $studentLesson ) )->Save();
        }


        /**
         * Set the challenge defaults
         */
        $challenge = [
            'isChallengeReady' => false,
            'isChallengeEOLReady' => false,
            'challenge_time' => null,
            'challenge_id' => null,
            'is_final' => false,
        ];

        /**
         * Set the challenge data "this current data"
         */
        if ($studentLesson) {
            if ($ChallengerResponse = Challenger::Ready($studentLesson, $this->getClassData('previousLessons'))) {
                $challenge['isChallengeReady'] = true;
                $challenge['challenge_time'] = $ChallengerResponse->challenge_time ?? '';
                $challenge['challenge_id'] = $ChallengerResponse->challenge_id ?? '';
                $challenge['is_final'] = $ChallengerResponse->is_final ?? false;
            }
        }

        if ($this->getClassData('student_unit_id') && $EOLStudentLesson = ClassroomQueries::EOLStudentLesson($this->getClassData('student_unit_id'))) {
            if ($ChallengerEOLResponse = Challenger::EOLReady($EOLStudentLesson, $this->getClassData('previousLessons'))) {
                // Note: this /may/mark StudentLesson->dnc_at ; then return null
                $challenge['isChallengeEOLReady'] = $ChallengerEOLResponse ? true : false;
                $challenge['challenge_time'] = $ChallengerEOLResponse->challenge_time ?? '';
                $challenge['challenge_id'] = $ChallengerEOLResponse->challenge_id ?? '';
            }
        }

        $this->setClassData('challenge', $challenge);

        if ($studentLesson) {
            $allChallenges = Challenge::where('student_lesson_id', $studentLesson->id)->get();
        } else {
            $allChallenges = [];
        }

        $this->setClassData('allChallenges', $allChallenges);


        /**
         * Checks if the student is ready for the exam
         * 'is_ready'        => [ true | false ]
         * 'next_attempt_at' => [ null | timestamp ]
         * 'missing_id_file' => [ true | false ]
         */
        $studentExam = $CourseAuth->ClassroomExam() ?? [];
        $this->setClassData('studentExam', $studentExam);

        if (is_numeric($CourseAuth->created_at)) {
            $CourseAuth->created_at = $CourseAuth->createdAt();
        }

        $this->setClassData('inst_unit_zoom_started', $CourseDate->InstUnit ? $CourseDate->InstUnit->zoom_started_at : null);

        /**
         * Chat Enabled
         */
        $this->setClassData('isChatEnabled', $CourseDate && ChatLogCache::IsEnabled($CourseDate->id));

        /**
         * Student Documents if there are documents required during the class set that here
         */
        $this->setClassData('documents', []);

        /**
         * Getting Started is a flag that is set when the student has
         * started the course
         */
        $this->setClassData('gettingStarted', false);
    }

    protected function setOfflineClassData($CourseAuth)
    {
        $this->setClassData('instructor', null);
        $this->setClassData('CourseDate', null);

        /**
         * Note this should only run after the update has run
         */
        if (self::SelfStudyLessons($CourseAuth)) {
            $this->setClassData('lessons', $lessons = self::SelfStudyLessons($CourseAuth)->toArray());
        } else {
            $this->setClassData('lessons', $lessons = $CourseAuth->GetCourse()->GetLessons()->toArray());
        }

        $this->setClassData('student_unit_id', null);
        $this->setClassData('is_live_class', false);
        $this->setClassData('courseUnitLessons', null);

        $this->setClassData('completedInstLessons', []);
        $this->setClassData('isZoomReady', false);
        $this->setClassData('studentLesson', null);
        $this->setClassData('lessonInProgress', false);


        /**
         * Get the previous completed lessons by the student
         */
        $this->setClassData('previousCompletedLessons', array_keys($CourseAuth->PCLCache()));


        /**
         * Get the lesson ids
         */
        $lessonIds = array_map(function ($lesson) {
            return $lesson['id'];
        }, $lessons);

        /**
         * Find lessons that are in both the course's lessons and
         * previously completed lessons
         */
        $completedStudentLessons = [];
        foreach ($lessonIds as $lessonId) {
            if (in_array($lessonId, $this->getClassData('previousCompletedLessons'))) {
                $completedStudentLessons[] = $lessonId;
            }
        }

        $this->setClassData('previousLessons', $completedStudentLessons && is_array($completedStudentLessons) ? $completedStudentLessons : []);
        $previousLessons = $this->getClassData('previousLessons');

        if (is_array($previousLessons) || $previousLessons instanceof Countable) {
            $this->setClassData('allCompletedStudentLessonsTotal', count($previousLessons));
        } else {
            // Handle the scenario when previousLessons is not countable, for example, set to 0 or log an error.
            $this->setClassData('allCompletedStudentLessonsTotal', 0);
        }

        $this->setClassData('allLessonsTotal', $CourseAuth->GetCourse()->GetLessons()->count());

        $this->setClassData('challenge', [
            'isChallengeReady' => false,
            'isChallengeEOLReady' => false,
            'challenge_time' => null,
            'challenge_id' => null,
            'is_final' => false,
        ]);

        $this->setClassData('GettingStarted', false);
        $this->setClassData('lessonPaused', false);

        /**
         * Get the Students Course
         */
        $this->setClassData('course', $CourseAuth->getCourse() ?? []);
        $this->setClassData('courseLessons', $lessons);


        /**
         * Student Exams
         */
        $this->setClassData('studentExam', $CourseAuth->ClassroomExam());

        /**
         * Student Documents
         */
        $this->setClassData('documents', $CourseAuth->GetCourse()->getDocs());

        /**
         * Getting Started is a flag that is set when the student has
         * started the course
         */
        $this->setClassData('gettingStarted', Auth::user()->GetPref("{$CourseAuth->id}:getting_started") ? true : false);

    }

    protected function initializeStudent($CourseAuth)
    {

        /**
         * @info` Need to be able to sync the student info and the course auth agreed_at
         * Check if the student has agreed to the terms
         * if not then we need to remove the student info
         */
        $user = $CourseAuth->GetUser();
        if ($CourseAuth->agreed_at === null) {
            if ($user->student_info !== null) {
                $user->student_info = null;
                $user->save();
            }
        } else if ($CourseAuth->agreed_at !== null && $user->student_info === null) {
            $CourseAuth->agreed_at = null;
            $CourseAuth->save();
        }
    }

    /**
     * Get the Classroom Data
     * React Endpont
     */
    public function getClassRoomData(Request $request, CourseAuth $CourseAuth)
    {
        if (!$CourseAuth) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid CourseAuth provided'
            ]);
        }


        if ($CourseAuth->disabled_at || $CourseAuth->completed_at) {
            return response()->json([
                'success' => false,
                'message' => 'Student Disabled',
                'redirect' => '/student/dashboard'
            ]);
        }

        $this->initializeStudent($CourseAuth);

        $this->setClassData('success', true);
        $this->setClassData('message', 'Class Room Data Loaded Successfully');

        /**
         * CourseAuth On and Offline access
         */
        $this->setClassData('courseAuth', $CourseAuth->toArray());

        /**
         * Generates the CourseDate Object this can be null
         * We can also say it there is no course date then the class is offline
         */
        $CourseDate = $CourseAuth->ClassroomCourseDate();

        if ($CourseDate) {
            $this->setLiveClassData($CourseAuth, $CourseDate);

            /**
             * Classroom is offline
             */
        } else {

            $this->setOfflineClassData($CourseAuth);
        }

        /**
         * Start and End Dates
         */
        $this->setClassData('created_at', Carbon::parse($CourseAuth->created_at)->isoFormat('MM-DD-YYYY HH:mm:ss'));
        $this->setClassData('starts_at', Carbon::parse($CourseAuth->start_date)->isoFormat('MM-DD-YYYY HH:mm:ss'));
        $this->setClassData('ends_at', $CourseAuth->expire_date ? Carbon::parse($CourseAuth->expire_date)->isoFormat('MM-DD-YYYY HH:mm:ss') : 'Open');
        $this->setClassData('completed_at', $CourseAuth->completed_at ? Carbon::parse($CourseAuth->completed_at)->isoFormat('MM-DD-YYYY HH:mm:ss') : 'Pending');

        /**
         * AppVersion
         */
        $this->setClassData('appVersion', Helpers::AppVersion());

        return response()->json($this->classroomData(), 200);
    }

    public function studentChallenge($student_lesson_id)
    {
        $Challenge = Challenge::create([
            'student_lesson_id' => $student_lesson_id
        ])->refresh();

        return response()->json($Challenge);
    }

    public function updateAgreement(Request $request)
    {
        $validated = $request->validate([
            'agreement' => 'required|boolean',
            'dob' => 'required|date_format:m/d/Y',
            'phone' => ['required', 'regex:/^(\d{10}|\d{3}-\d{3}-\d{4})$/'], // Correctly delimited and escaped
            'student_id' => 'required|integer',
            'course_auth_id' => 'required|integer'
        ]);


        $user_id = $request->input('student_id');
        $course_auth_id = $request->input('course_auth_id');

        DB::beginTransaction(); // Start the transaction

        try {
            $auth = CourseAuth::where('user_id', $user_id)
                ->where('id', $course_auth_id)->firstOrFail();

            $auth->agreed_at = Carbon::now();
            $auth->save();

            $user = User::findOrFail($user_id);

            // Update user data directly, ensuring that the data structure is correct and consistent
            $user->update([
                'student_info' => [
                    'fname' => $request->input('fname'),
                    'initial' => $request->input('initial'),
                    'lname' => $request->input('lname'),
                    'phone' => $request->input('phone'),
                    'dob' => Carbon::createFromFormat('m/d/Y', $request->input('dob'))->toDateString(), // Ensure consistent date storage format
                    'suffix' => $request->input('suffix'),
                ]
            ]);

            DB::commit(); // Commit the transaction
            return response()->json(['success' => true, 'message' => 'Agreement updated successfully']);
        } catch (\Throwable $e) { // Catch more general exception to handle ModelNotFoundException and others
            DB::rollBack(); // Rollback the transaction
            Log::error('Update agreement failed', ['error' => $e->getMessage(), 'user_id' => $user_id]); // Log the error
            return response()->json(['success' => false, 'message' => 'Update failed. ' . $e->getMessage() . ' Please try again.'], 500); // Return a more generic error message to the user
        }
    }

    public function studentMarkCompleted(Request $request)
    {
	    #Challenger::MarkCompleted($request->challenge_id);
        Challenger::MarkCompleted($request->input('challenge_id'));
        return response()->json(['success' => true]);
    }

    public function studentChallengeExpired(Request $request)
    {
        #Challenger::MarkFailed($request->challenge_id);
        Challenger::MarkFailed($request->input('challenge_id') );
        return response()->json(['success' => true]);
    }

    public function StudentPlayer()
    {
        $content = array_merge([], self::renderPageMeta('zoom_meetings'));
        return view('frontend.students.zoomplayer', compact('content'));
    }

    /**
     * Store the uploaded id card or headshot for the student
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveIdData(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'student_unit_id' => ['required', 'not_in:0', 'integer'],
                'course_auth_id' => ['required', 'not_in:0', 'integer'],
                'photoType' => 'required|string|in:idcard,headshot',
                'file' => 'required|file|mimes:jpeg,png,gif,heic|max:4096',
            ]);

            $file = $request->file('file');
            $photoType = $request->input('photoType');
            $path = '';

            if ($photoType === 'idcard') {
                $Validation = Validation::updateOrCreate([
                    'course_auth_id' => $request->course_auth_id,
                ])->refresh();
            } elseif ($photoType === 'headshot') {
                $Validation = Validation::updateOrCreate([
                    'student_unit_id' => $request->student_unit_id,
                ])->refresh();
            }

            $relPath = $Validation->RelPath();
            $directory = dirname($relPath);
            $fileName = basename($relPath);

            // Store the file and verify it was stored successfully
            $path = $file->storeAs($directory, $fileName);

            if (!$path) {
                // If storage was not successful, return an error response
                return response()->json([
                    'message' => 'Failed to upload the file.'
                ], 500);
            }

            return response()->json([
                'message' => 'File uploaded successfully',
                'path' => $path,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error occurred',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // It might be useful to log the exception message for debugging
            Log::error("Upload error: " . $e->getMessage());
            return response()->json([
                'message' => 'Internal server upload error occurred'
            ], 500);
        }
    }


    public function getZoomData($course_date_id, $course_auth_id)
    {
        if (!$InstUnit = InstUnit::firstWhere('course_date_id', $course_date_id)) {
            return response()->json([
                'message' => 'No Instructor Unit Set',
                'zoom' => null,
            ], 200);
        }

        if ($InstUnit->completed_at !== null) {
            return response()->json([
                'message' => 'Class has Ended',
                'zoom' => null,
            ], 200);
        }

        $ZoomCreds = $InstUnit->GetCreatedBy()->ZoomCreds;

        if ($ZoomCreds->zoom_status == "disabled") {
            return response()->json([
                'message' => 'Zoom Not Ready',
                'zoom' => null,
            ], 200);
        }

        $zoom = [
            'meeting_id' => $ZoomCreds->pmi,
            'meeting_passcode' => Crypt::decrypt($ZoomCreds->zoom_passcode)
        ];

        return response()->json([
            'message' => 'Zoom data retrieved successfully',
            'zoom' => $zoom,
        ], 200);
    }

    public function closeLesson(Request $request)
    {
        $StudentLesson = StudentLesson::find($request->lesson_id);

        return response()->json([
            'message' => 'Lesson closed successfully',
            'studentUnit' => $StudentLesson,
        ], 200);
    }

    public function getCurrentCourseDateID(CourseAuth $CourseAuth): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'course_date_id' => $CourseAuth->ClassroomCourseDate()->id ?? null,
        ]);
    }

    public function setBrowser(Request $request)
    {
        try {
            Auth::user()->setBrowser($request->browser);

            return response()->json([
                'success' => true,
                'message' => 'Browser set successfully',
            ]);
        } catch (ValidationException $e) {
            // Handle the validation exception and return a custom error response
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }
    public function generateZoomSignature($meetingNumber, $role) {
        $sdkKey = config('zoom.api_key');
        $sdkSecret = config('zoom.api_secret');
        $iat = time();
        $exp = $iat + 3600; // Signature valid for 1 hour

        $payload = [
            'sdkKey' => $sdkKey,
            'mn' => $meetingNumber,
            'role' => $role,
            'iat' => $iat,
            'exp' => $exp,
            'tokenExp' => $exp
        ];

        return JWT::encode($payload, $sdkSecret, 'HS256');
    }

} // End of Class
