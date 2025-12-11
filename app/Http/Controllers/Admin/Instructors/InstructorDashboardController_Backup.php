<?php
declare(strict_types=1);
namespace App\Http\Controllers\Admin\Instructors;

use App\Models\Course;
use DB;
use Auth;
use File;
use Session;
use Carbon\Carbon;
use App\Models\User;
use App\Helpers\Helpers;
use App\Models\InstUnit;
use App\Models\Validation;
use App\Models\CourseDate;
use App\Models\CourseAuth;
use App\Models\InstLesson;
use App\Models\StudentUnit;
use Illuminate\Http\Request;
use App\Classes\ChatLogCache;
use App\Models\StudentLesson;
use App\Classes\TrackingQueries;
use App\Classes\VideoCallRequest;
use App\Traits\PageMetaDataTrait;
use App\Classes\ClassroomQueries;
use App\Classes\ValidationsPhotos;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use KKP\Laravel\Traits\StoragePathTrait;
use Illuminate\Validation\ValidationException;


class InstructorDashboardController extends Controller
{
    use PageMetaDataTrait;
    use StoragePathTrait;

    public $classData = [];

    public function __construct()
    {
        // Make sure that the validation directories are created
        $this->AssertStoragePath('idcards');
        $this->AssertStoragePath('headshots');
    }

    /*********************** */
    /* View Outputs          */
    /*********************** */

    public function dashboard()
    {
        $content = array_merge([], self::renderPageMeta('instructor_dashboard'));
        return view('admin.instructors.dashboard', compact('content'));
    }

    public function zoomMeeting()
    {
        $content = array_merge([], self::renderPageMeta('zoom_meetings'));
        return view('admin.instructors.zoom-meeting', compact('content'));
    }




    /*********************** */
    /* End Points            */
    /*********************** */

    public function validateInstructorSession()
    {
        $authUser = Auth::user();
        if (!$authUser) {
            return response()->json(['success' => false, 'message' => 'Unauthorized User'], 401);
        }

        $CourseDates = ClassroomQueries::InstructorDashboardCourseDates();

        // Extract and merge 'created_by' and 'assistant_id' from InstUnit
        $createdByIds = $CourseDates->pluck('InstUnit.created_by')->filter()->unique();
        $assistantIds = $CourseDates->pluck('InstUnit.assistant_id')->filter()->unique();
        $userIds = $createdByIds->merge($assistantIds)->unique();

        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        $data = [
            'success' => true,
            'courses' => [],
            'instructor' => $authUser
        ];

        foreach ($CourseDates as $index => $course) {
            $data['courses'][$index] = [
                'id' => $course->id,
                'title' => $course->GetCourseUnit()->LongTitle(),
                'starts_at' => $course->StartsAt(),
                'ends_at' => $course->EndsAt()
            ];

            if ($course->InstUnit !== null) {
                $data['courses'][$index]['InstUnit'] = $course->InstUnit->toArray();
                $data['courses'][$index]['createdBy'] = isset($users[$course->InstUnit->created_by]) ? $users[$course->InstUnit->created_by]->fullname() : null;
                $data['courses'][$index]['assistantBy'] = isset($users[$course->InstUnit->assistant_id]) ? $users[$course->InstUnit->assistant_id]->fullname() : null;
            }
        }

        // Assign user role based on the last course, if applicable
        if (!empty($CourseDates) && ($lastCourse = $CourseDates->last()) && $lastCourse->InstUnit) {
            $data['instructor']['userRole'] = $authUser->id === $lastCourse->InstUnit->created_by ? 'instructor' : 'assistant';
        } else {
            $data['instructor']['userRole'] = 'unknown'; // Or set a default role
        }

        if (empty($data['courses'])) {
            $data['success'] = false;
            $data['message'] = "No Active Courses Found!";
        }

        return response()->json($data, 200);
    }

    /**
     * Polls the server for updates on the classroom sessions
     *
     * @polling: every - 30sec - reFocus - StaleData
     * @param mixed $course_date_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClassData()
    {
        /**
         * CourseDates are only available in a live classroom
         */
        $CourseDates = ClassroomQueries::InstructorDashboardCourseDates();

        /**
         * Begin the response
         */
        $this->setClassData('success', true);
        $this->setClassData('message', 'Classroom Data Fetched Successfully');

        if ($CourseDates->count() > 0) {
            foreach ($CourseDates as $index => $CourseDate) {

                if (
                    $CourseDate->InstUnit && $CourseDate->InstUnit->created_by == Auth()->user()->id ||
                    $CourseDate->InstUnit && $CourseDate->InstUnit->assistant_id == Auth()->user()->id
                ) {

                    /**
                     * Set CourseDate and Instructor
                     * select by the instructor.
                     */
                    $this->setClassData('courseDate', $CourseDate);
                    $this->setClassData('instructor', $this->assignedInstructor($CourseDate));

                    /**
                     * The Current Active Lesson is null unless there is an active lesson
                     */
                    $instructorLesson = null;

                    /**
                     * The Instructor Unit
                     */
                    $this->setClassData('instUnit', $CourseDate->instUnit->toArray());

                    /**
                     * The Active Lesson bool
                     */
                    $instructorLesson = ClassroomQueries::ActiveInstLesson($CourseDate->instUnit);

                    /**
                     * The Active Lesson
                     */
                    if ($instructorLesson !== null) {

                        if (Auth()->user()->id == 2) {

                            /**
                             * The Instructor Lesson created_at date this format doesnt work
                             * But it allows me to test with out waiting for the time to pass
                             */
                            $instructorLesson->created_at = $instructorLesson->createdAt();
                        } else {
                            /**
                             * The Instructor Lesson created_at date
                             */
                            $instructorLesson->created_at = Carbon::parse($instructorLesson->created_at)->isoFormat('YYYY-MM-DD HH:mm:ss');
                        }

                        /**
                         * The Active Lesson
                         */
                        $this->setClassData('instUnitLesson', $instructorLesson);
                        $this->setClassData('instructorCanClose', $instructorLesson->instCanClose());

                    } else {

                        /**
                         * Active Lesson is null
                         */
                        $this->setClassData('instUnitLesson', null);
                        $this->setClassData('instructorCanClose', false);
                    }

                    /**
                     * The Instructors Completed Lessons
                     */
                    $this->setClassData('completedLessons', InstLesson::select(['lesson_id', 'completed_at'])
                        ->where('inst_unit_id', $CourseDate->instUnit->id)
                        ->where('completed_at', '!=', null)
                        ->get());

                    /**
                     * Get Course Data  based off the CourseDate
                     */
                    $this->setClassData('course', $CourseDate->getCourse()->toArray());

                    /**
                     * The Course Unit Lessons
                     * This alos returns the list of lesson associated with the course
                     */
                    $this->setClassData('courseUnitLessons', $CourseDate->getCourseUnit()->getCourseUnitLessons());

                    /**
                     * The Chat Log
                     */
                    $this->setClassData('isChatEnabled', ChatLogCache::IsEnabled((int) $CourseDate->id));

                    /**
                     * Returns the Current Day Lessons if returned by CourseDate
                     */
                    $this->setClassData('lessons', $CourseDate->getCourseUnit()->getLessons()->toArray());
                    /**
                     * Assistant Data
                     */
                    $this->setClassData('assistant', $this->getAssistant($CourseDate->InstUnit->assistant_id ?? null));

                    /**
                     * The Agora Video Call Request
                     */
                    $this->setClassData('callRequest', VideoCallRequest::queue($CourseDate->id));
                    $this->setClassData('appVersion', Helpers::AppVersion());
                }
            }
        } else {
            // OFFLINE CLASSROOM

            $this->setClassData('instructor', $instructor = $this->assignedInstructor(null));
            $this->setClassData('courseDate', $CourseDates);

            /**
             * The Course is offline so there is no instUnit
             */
            $this->setClassData('instUnit', null);
            $this->setClassData('instUnitLesson', null);
            $this->setClassData('instructorCanClose', false);

            $this->setClassData('completedLessons', collect([]));
            $this->setClassData('course', null);
            $this->setClassData('courseUnitLessons', []);
            $this->setClassData('isChatEnabled', false);
            $this->setClassData('lessons', []);
            $this->setClassData('assistant', null);
            $this->setClassData('studentUnit', []);
            $this->setClassData('callRequest', []);
            $this->setClassData('totalStudentsCount', 0);
            $this->setClassData('completedStudentsCount', 0);
            $this->setClassData('appVersion', Helpers::AppVersion());
        }

        /**
         * Adding thre 0 is a hack the ReactApp utilize the zero index
         */
        $classData[0] = $this->classroomData();
        return response()->json($classData, 200);
    }

    /**
     * Get the Assistant
     * @param mixed $assistant_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssistant($assistant_id)
    {
        if (!$assistant_id) {
            return response()->json(null);
        }

        $assistant = User::find($assistant_id);
        $assistant->avatar = $assistant->getAvatar('thumb');
        return response()->json($assistant);
    }

    /**
     * Get ALl the Student and Organizes them Into Validation Completion status
     */
    public function getStudents(CourseDate $CourseDate, $page, $search = null)
    {
        $ActiveStudentUnits = ClassroomQueries::ActiveStudentUnits($CourseDate);

        $studentTypes = [
            'verified',
            'unverified',
            'inactive'
        ];

        if (empty($ActiveStudentUnits)) {
            return response()->json([
                'success' => false,
                'message' => "No Students have entered the classroom yet!",
                'verified' => null,
                'unverified' => null,
                'inactive' => null
            ]);
        }

        foreach ($studentTypes as $type) {
            $students[$type] = $this->getStudentsByType($ActiveStudentUnits, $CourseDate->id, $type, $page, $search);
        }

        /**
         * Student Data
         */
        $studentUnits = StudentUnit::where('course_date_id', $CourseDate->id)->get();
        $this->setClassData('studentUnits', $studentUnits);

        $this->setClassData('totalStudentsCount', $studentUnits->count());
        $studentUnitCompleted = $studentUnits->where('completed_at', '!=', null);
        $this->setClassData('completedStudentsCount', $studentUnitCompleted->count());



        return response()->json([
            'success' => true,
            'message' => "Students retrieved successfully!",
            ...$students
        ]);
    }

    public function reassignInstructor(Request $request)
    {
        $request->validate([
            'courseDateId' => 'required',
        ]);

        $InstUnit = InstUnit::where('course_date_id', $request->courseDateId)->first();

        if (!$InstUnit) {
            return response()->json([
                'success' => false,
                'message' => "InstUnit not found for given Course Date ID",
            ]);
        }

        $InstUnit->created_by = auth()->user()->id;
        $InstUnit->save();

        return response()->json([
            'success' => true,
            'message' => "Instructor Reassigned Successfully",
            'id' => auth()->user()->id,
        ]);
    }

    /**
     * Validate the student's ID and perform 
     * necessary actions based on the request.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateStudentID(Request $request)
    {
        // 1. Input Validation
        $validatedData = $request->validate([
            'course_auth_id' => 'required|string',
            'course_date_id' => 'required|int',
            'instructor_id' => 'required|int',
            'course_id' => 'required|string',
            'type' => 'required|string',
            'validate_type' => 'required|string',
            'validation_action' => 'required|string|in:verified,declined', // 'verified' or 'declined'
            'customSlider' => 'sometimes|required|in:0,2', // Only validate if present
        ]);

        // Determine validation_action based on customSlider value if present
        if (isset($validatedData['customSlider'])) {
            $validatedData['validation_action'] = $validatedData['customSlider'] == 2 ? 'valid' : 'delete';
        }

        // If validation_action is delete, handle photo removal
        if ($validatedData['validation_action'] === 'delete') {
            $photoRemoved = $this->removeStudentPhoto($request);

            if ($photoRemoved['success'] === false) {
                return response()->json([
                    'success' => false,
                    'message' => $photoRemoved['message']
                ], 200);
            }
        }

        try {
            // 2. Error Handling & Using Transactions
            DB::beginTransaction();

            // 3. Optimize Database Queries
            $student = StudentUnit::where('course_auth_id', $validatedData['course_auth_id'])
                ->where('course_date_id', $validatedData['course_date_id'])
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => "Student not found"
                ], 200);
            }

            $instructor = User::find($validatedData['instructor_id']);

            if (!$instructor) {
                return response()->json([
                    'success' => false,
                    'message' => "Instructor Not Found"
                ], 200);
            }

            // Update student verification status
            $student->verified = [
                'instructor' => $instructor->fullname(),
                'timestamp' => Carbon::now()->toIso8601String(),
                'course_id' => $validatedData['course_id'],
                'type' => $validatedData['type'],
                'message' => $validatedData['validation_action'] === "verified" ? $request->input('message', '') : "",
                'license_type' => $validatedData['validate_type'],
                'status' => $validatedData['validation_action'],
            ];

            $student->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Student Validated"
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }


    public function updateZoomData(Request $request)
    {
        $CourseDate = CourseDate::find($request->input('course_date_id'));
        $zoom = $CourseDate->InstUnit->GetCourse()->ZoomCreds;

        $zoom->zoom_status = $request->zoomStatus;
        $zoom->save();

        $data['payload'] = $zoom;
        $data['success'] = true;
        return response()->json($data);
    }

    /**
     * @desc: Activate Lesson
     * @param Request $request
     */
    public function activateLesson(Request $request)
    {
        $instUnit = InstUnit::where('course_date_id', $request->course_date_id)->get()->first();
        TrackingQueries::InitInstLesson($instUnit, $request->lesson_id);

        $data['success'] = true;
        $data['message'] = "Lesson Activated";
        return response()->json($data);
    }

    /**
     * Assigns the instructor to the active class
     * @param Request $request
     * @param CourseDate $CourseDate
     */
    public function assignInstructor(Request $request, CourseDate $CourseDate): \Illuminate\Http\JsonResponse
    {
        if (!$CourseDate->InstUnit) {
    
            try {
               // Initialize the InstUnit after starting the server
                ClassroomQueries::InitInstUnit($CourseDate);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage(),
                ], 500);
            }
        }
    
        $data['success'] = true;
        $data['id'] = $CourseDate->InstUnit->created_by ?? 0;
        $data['message'] = "Instructor assigned to class";
    
        return response()->json($data);
    }    

    /**
     * @desc: Complete Lesson
     * @param Request $request
     */
    public function completeLesson(Request $request)
    {
        $instUnit = InstUnit::firstWhere('course_date_id', $request->input('course_date_id'));

        InstLesson::where([
            'inst_unit_id' => $instUnit->id,
            'lesson_id' => $request->input('lesson_id')
        ])->update([
            'completed_at' => Carbon::now(),
            'completed_by' => Auth()->id(),
        ]);

        $data['success'] = true;
        $data['message'] = "Lesson Completed";
        return response()->json($data);
    }

    public function completedCourseData($course_date_id)
    {
        return Cache::remember("completedCourseData-{$course_date_id}", 30, function () use ($course_date_id) {
            $studentUnits = StudentUnit::where('course_date_id', $course_date_id)->get();

            $completedUnitsCount = [];
            $dncUnitsCount = [];
            foreach ($studentUnits as $studentUnit) {
                if ($studentUnit->completed_at) {
                    $completedUnitsCount[$studentUnit->student_id] = isset($completedUnitsCount[$studentUnit->student_id])
                        ? $completedUnitsCount[$studentUnit->student_id] + 1
                        : 1;
                } else {
                    $dncUnitsCount[$studentUnit->student_id] = isset($dncUnitsCount[$studentUnit->student_id])
                        ? $dncUnitsCount[$studentUnit->student_id] + 1
                        : 1;
                }
            }

            // Convert the completed and DNC units counts to JSON and send the response
            return response()->json([
                'completedUnitsCount' => count($completedUnitsCount),
                'dncUnitsCount' => count($dncUnitsCount),
            ]);
        });
    }

    /**
     * @desc: Pause Lesson
     */
    public function pauseLesson(Request $request, $status)
    {
        $instUnit = InstUnit::where('course_date_id', $request->input('courseDateId'))->first();
        $instLesson = InstLesson::where('inst_unit_id', $instUnit->id)
            ->where('lesson_id', $request->input('lessonId'))->first();

        if ($instLesson) {
            $instLesson->is_paused = $status == 'true';
            $instLesson->save();

            $data['success'] = true;
            $data['message'] = "Lesson Paused";
            return response()->json($data);
        } else {
            $data['success'] = false;
            $data['message'] = "Could Not Pause Lesson";
            return response()->json($data);
        }
    }

    /**
     * Summary of getStudentByID
     * @param  $student_id
     * @return mixed
     */
    public function getStudentByID($student_id, $course_date_id)
    {
        $data['success'] = false;
        $data['message'] = "";

        // Fetch the student by ID
        $student = User::find((int) $student_id);

        // Check if the student exists
        if (!$student) {
            $data['message'] = "Invalid Student, could not find student with this id: " . $student_id;
            return response()->json($data);
        }

        // Assign avatar to student
        $student->avatar = $student->getAvatar('thumb');

        // Initialize course array to prevent undefined variable issues
        $courses = [];
        // Fetch all course authorizations for the student
        $courseAuths = CourseAuth::where('user_id', $student_id)->get();

        // Check if course authorizations exist
        if (!$courseAuths->isEmpty()) {
            foreach ($courseAuths as $courseAuth) {
                $courses[$courseAuth->id] = $courseAuth->GetCourse(); // Safe to access now
            }

            // Set class data for courses and courseAuths
            $this->setClassData('courses', $courses);
            $this->setClassData('courseAuths', $courseAuths);

            // Fetch the relevant course authorization for the specific course date
            $studentUnit = null;
            foreach ($courseAuths as $courseAuth) {
                $studentUnit = $courseAuth->studentUnits()
                    ->where('course_date_id', $course_date_id)
                    ->first();

                if ($studentUnit) {
                    break;
                }
            }

            // Ensure $courseAuth is defined and related to the found student unit
            if ($studentUnit) {

                // Fetch the course unit for the student unit
                $courseUnit = $studentUnit->GetCourseUnit();

                // Fetch the course authorization for the student
                $courseAuth = CourseAuth::where('user_id', $student_id)
                    ->where('course_id', $courseUnit->course_id)
                    ->first();

                // Assign fetched data to student object
                $student->studentUnit = $studentUnit;
                $student->student_unit_id = $studentUnit->id;
                $student->course_date_id = $course_date_id;
                $student->course_id = $courseAuth->course_id;
                $student->course_auth_id = $courseAuth->id;

                // Fetch validations
                $idcardValidation = Validation::where('course_auth_id', $courseAuth->id)->first();
                $headshotValidation = Validation::where('student_unit_id', $student->student_unit_id)->first();

                // Default URL for validations
                $defaultUrl = (new Validation())->URL(true);

                // Assign validation data to student object
                $student->validations = [
                    'idcard' => $idcardValidation && Storage::disk('public')->exists($idcardValidation->RelPath())
                        ? vasset("storage/" . $idcardValidation->RelPath())
                        : $defaultUrl,
                    'idcard_status' => $idcardValidation ? $idcardValidation->status : 0,

                    'headshot' => $headshotValidation && Storage::disk('public')->exists($headshotValidation->RelPath())
                        ? vasset("storage/" . $headshotValidation->RelPath())
                        : $defaultUrl,
                    'headshot_status' => $headshotValidation ? $headshotValidation->status : 0,
                ];

                // Assign student unit lessons if student unit exists
                $student->studentUnitLessons = $studentUnit ? $studentUnit->StudentLessons->toArray() : [];

                $data['success'] = true;
                $data['message'] = "Student Loaded";
                $data['student'] = $student;
            } else {
                $data['message'] = "No course authorizations found for this student on the given date.";
            }
        } else {
            $data['message'] = "No course authorizations found for this student.";
        }

        // Return JSON response
        return response()->json($data);
    }

    /**
     * Remove the validation Photo
     */
    public function removeStudentPhoto(Request $request)
    {
        // Assuming $request->file contains the full URL, we need to extract the relative path
        // This depends on how your URLs are structured, adjust the pattern as necessary
        $pattern = '/https?:\/\/[^\/]+\/storage\/(.+)/';
        if (preg_match($pattern, $request->file, $matches)) {
            // Extracted the relative path from the URL
            $relativeFilePath = $matches[1];
        } else {
            // URL did not match expected pattern
            return response()->json(['success' => false, 'message' => "Invalid file path format"]);
        }

        // Construct the full path to the file within the storage directory
        $fullFilePath = storage_path('app/public/' . $relativeFilePath);
        // remove query string
        $fullFilePath = preg_replace('/\?.*/', '', $fullFilePath);

        if (file_exists($fullFilePath)) {
            // Use File::delete() from the Illuminate\Support\Facades\File namespace
            if (File::delete($fullFilePath)) {
                $data['success'] = true;
                $data['message'] = "Photo Removed";
            } else {
                // In case File::delete() returns false
                $data['success'] = false;
                $data['message'] = "Failed to delete photo: " . $fullFilePath;
            }
        } else {
            $data['success'] = false;
            $data['message'] = "Photo Not Found: " . $fullFilePath;
        }

        return response()->json($data);
    }

    public function completeCourse(Request $request)
    {
        #$InstUnit = InstUnit::where('course_date_id', $request->course_date_id)->get()->first();
        $InstUnit = InstUnit::firstWhere('course_date_id', $request->course_date_id);
        TrackingQueries::CompleteInstUnit($InstUnit);

        $InstUnit->GetCourse()->ZoomCreds->update(['zoom_status' => 'disabled']);

        Session::remove('zoomDisabled');

        $data['success'] = true;
        $data['message'] = "Course Closed";
        return response()->json($data);
    }

    public function assignAssistant(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'courseDateId' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 500);
        }

        $InstUnit = InstUnit::where('course_date_id', $request->courseDateId)->first();

        if (!$InstUnit) {
            return response()->json([
                'success' => false,
                'message' => "InstUnit not found for given Course Date ID",
            ]);
        }

        if ($request->has('type') && $request->input('type') == "leave") {
            $InstUnit->assistant_id = null;
        } else {
            $InstUnit->assistant_id = auth()->user()->id;
        }

        $InstUnit->save();

        return response()->json([
            'success' => true,
            'message' => "Assistant Assigned Successfully",
            'id' => auth()->user()->id,
        ]);
    }

    /**
     * Allows access if the uses is in the Lesson in Progress Page
     */
    public function allowAccess($student_unit_id)
    {

        if (!$StudentUnit = StudentUnit::find($student_unit_id)) {
            return response()->json([
                'success' => false,
                'message' => "Student Unit not found for given ID",
            ]);
        }

        if (!$InstLesson = TrackingQueries::ActiveInstLesson($StudentUnit->InstUnit)) {
            return response()->json([
                'success' => false,
                'message' => "No active lesson found for given student unit",
            ]);
        }

        if (
            StudentLesson::where('student_unit_id', $student_unit_id)
                ->where('lesson_id', $InstLesson->lesson_id)
                ->where('inst_lesson_id', $InstLesson->id)
                ->first()
        ) {
            return response()->json([
                'success' => false,
                'message' => "Student already has access to this lesson",
            ]);
        }

        StudentLesson::create([
            'student_unit_id' => $student_unit_id,
            'lesson_id' => $InstLesson->lesson_id,
            'inst_lesson_id' => $InstLesson->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Student has been granted access to this lesson",
        ]);
    }

    public function revokeDNC(Request $request)
    {
        $student_unit_id = $request->input('studentUnitId');
        $active_lesson = $request->input('lessonId');

        $studentLesson = StudentLesson::where('student_unit_id', $student_unit_id)
            ->where('lesson_id', $active_lesson)
            ->first();

        $studentLesson->dnc_at = null;
        $studentLesson->save();

        return response()->json([
            'success' => true,
            'message' => "Student DNC Revoked",
        ]);
    }

    public function banStudent(Request $request, $studentUnitId)
    {
        // validate
        $request->validate([
            'banReason' => 'required|string',
        ]);


        $studentUnit = StudentUnit::find($studentUnitId);

        if (!$studentUnit) {
            return response()->json([
                'success' => false,
                'message' => "Student Unit not found for given ID",
            ]);
        }

        $courseAuth = CourseAuth::find($studentUnit->course_auth_id);
        $courseAuth->disabled_at = Carbon::now();
        $courseAuth->disabled_reason = $request->input('banReason');
        $courseAuth->save();

        return response()->json([
            'success' => true,
            'message' => "Student Banned",
        ]);
    }

    /*********************** */
    /* Internal Functions    */
    /*********************** */

    protected function getStudentsByType($ActiveStudentUnits, $courseDateId, $type, $page, $search)
    {
        switch ($type) {
            case 'verified':
                return $this->getVerifiedStudents($ActiveStudentUnits, $courseDateId, $page, $search);
            case 'unverified':
                return $this->getUnVerifiedStudents($ActiveStudentUnits, $courseDateId, $page, $search);
            case 'inactive':
                return $this->getBannedStudents($ActiveStudentUnits, $courseDateId, $page, $search);
            default:
                return [];
        }
    }

    protected function getStudentDetailsByType($studentUnits, $type)
    {
        if ($studentUnits->isEmpty()) {
            return [];
        }

        $filteredAuthUsers = collect();

        // Fetch all related CourseAuth records in one go to optimize performance
        $courseAuthIds = $studentUnits->pluck('course_auth_id')->unique();
        $courseAuths = CourseAuth::select('id', 'user_id', 'disabled_at')
            ->whereIn('id', $courseAuthIds)
            ->get()
            ->keyBy('id');

        if ($type === 'verified' || $type === 'unverified') {
            // Filter for verified or unverified, but exclude any that are disabled
            $filteredAuthUsers = $studentUnits->filter(function ($studentUnit) use ($type, $courseAuths) {
                $courseAuth = $courseAuths->get($studentUnit->course_auth_id);
                $isDisabled = $courseAuth && !is_null($courseAuth->disabled_at);
                $isVerified = $studentUnit->verified;

                if ($isDisabled) {
                    return false; // Exclude disabled students
                }

                return ($type === 'verified' && $isVerified) || ($type === 'unverified' && !$isVerified);
            });
        } elseif ($type === 'inactive') {
            // Specifically include only disabled students for 'inactive'
            $filteredAuthUsers = $studentUnits->filter(function ($studentUnit) use ($courseAuths) {
                $courseAuth = $courseAuths->get($studentUnit->course_auth_id);
                return $courseAuth && !is_null($courseAuth->disabled_at);
            });
        }

        // Prepare final data set
        return $filteredAuthUsers->map(function ($studentUnit) use ($courseAuths) {
            $courseAuth = $courseAuths->get($studentUnit->course_auth_id);
            return [
                'user_id' => $courseAuth ? $courseAuth->user_id : null,
                'course_auth_id' => $studentUnit->course_auth_id,
                'course_date_id' => $studentUnit->course_date_id,
            ];
        })->all();
    }

    /**
     * Get Verified Students
     * @param mixed $authorized
     */
    protected function getVerifiedStudents($authorized, $courseDateId, $page = 1, $search = null)
    {
        $studentGroups = $this->getStudentDetailsByType($authorized, 'verified');

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $students = $this->getStudentData($studentGroups, $courseDateId, $search);
        return $students;
    }

    protected function getUnVerifiedStudents($authorized, $courseDateId, $page = 1, $search = null)
    {
        $studentGroups = $this->getStudentDetailsByType($authorized, 'unverified');

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $students = $this->getStudentData($studentGroups, $courseDateId, $search);
        return $students;
    }

    protected function getBannedStudents($authorized, $courseDateId, $page = 1, $search = null)
    {
        $studentGroups = $this->getStudentDetailsByType($authorized, 'inactive');

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $students = $this->getStudentData($studentGroups, $courseDateId, $search);
        return $students;
    }

    /**
     * Student Ids can be an array or a single id
     * search is a string to search the student
     *
     */
    protected function getStudentData($studentIds, $courseDateId, $search)
    {
        if (empty($studentIds)) {
            return collect();  // Return an empty collection if no IDs are provided
        }

        $query = User::query();

        /* Adjust the query based on the type of studentIds provided */
        if (is_array($studentIds)) {
            $userIds = array_column($studentIds, 'user_id');
            $query->whereIn('id', $userIds);
        } else {
            $query->where('id', $studentIds);
        }

        if (!is_null($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('fname', 'like', '%' . $search . '%')
                    ->orWhere('lname', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Get the paginated result
        $paginatedStudents = $query->paginate(20);

        // Map over the collection inside the paginator
        $transformedStudents = $paginatedStudents->getCollection()->map(function ($student) use ($courseDateId) {

            $courseAuths = $student->courseAuths();
            $courseAuth = $courseAuths->where('user_id', $student->id)->first();

            /**
             * If we geting the studentUnit
             */
            $studentUnit = StudentUnit::where('course_auth_id', $courseAuth->id)
                ->where('course_date_id', $courseDateId)
                ->whereDate('created_at', Carbon::today()->isoFormat('YYYY-MM-DD'))
                ->get()->first();

            $idcard = Validation::where('course_auth_id', $courseAuth->id)
                ->first();


            if ($studentUnit) {
                $headshot = Validation::where('student_unit_id', $studentUnit->id)
                    ->first();
            } else {
                $headshot = null;
            }

            $validations = [
                'idcard' => $idcard ? vasset("storage/" . $idcard->RelPath()) : (new Validation())->URL(true),
                'headshot' => $headshot ? vasset("storage/" . $headshot->RelPath()) : (new Validation())->URL(true),
            ];

            $student->avatar = $student->getAvatar('thumb');
            $student->created_at = Carbon::parse($student->created_at)->toDateTimeString();
            $student->updated_at = Carbon::parse($student->updated_at)->toDateTimeString();
            $student->course_auth_id = $courseAuth->id;
            $student->course_date_id = $courseDateId;
            $student->student_unit_id = $studentUnit ? $studentUnit->id : null;
            $student->validations = $validations;
            $student->courseAuth = $courseAuth;
            $student->studentUnit = (is_null($studentUnit) ? [] : $studentUnit->toArray());
            $student->studentLessons = $this->getStudentUnitLessons($studentUnit);

            return $student;
        });

        // Replace the original collection in the paginator with the transformed one
        $paginatedStudents->setCollection($transformedStudents);

        return $paginatedStudents;
    }

    protected function getStudentUnitLessons($studentUnit)
    {

        if (!$studentUnit) {
            return [];
        }

        $studentUnitLessons = StudentLesson::where('student_unit_id', $studentUnit->id)->get();

        $studentUnitLessons->map(function ($studentUnitLesson) {
            $studentUnitLesson->lesson->completed = $studentUnitLesson->completed_at ? true : false;
            $studentUnitLesson->lesson->completed_by = $studentUnitLesson->completed_by;
            $studentUnitLesson->lesson->completed_at = $studentUnitLesson->completed_at;
            return $studentUnitLesson;
        });

        return $studentUnitLessons;
    }

    /**
     * Validate the Initial Session
     * This has no Polling however we do make request back
     * onFocus to keep and stale data upto date
     * stale time is for 30sec
     */
    protected function assignedInstructor($CourseDate)
    {
        $instructor = null;

        if ($CourseDate && $CourseDate->InstUnit && $CourseDate->InstUnit->created_by) {
            $instructor = $CourseDate->InstUnit->GetCreatedBy();
            $instructor->avatar = $instructor->getAvatar('thumb');

            $authenticatedUserId = Auth::id();
            $instructorCreatorId = $CourseDate->InstUnit->created_by;

            $instructor['userRole'] = ($authenticatedUserId === $instructorCreatorId) ? 'instructor' : 'assistant';
            $instructor['instUnit'] = $CourseDate->InstUnit->toArray();

            if ($zoom = $CourseDate->InstUnit->GetCourse()->ZoomCreds) {
                $zoom->zoom_passcode = Crypt::decrypt($zoom->zoom_passcode);
                $zoom->zoom_password = Crypt::decrypt($zoom->zoom_password);
                $instructor->zoom_payload = $zoom->toArray();
            }
        }

        return $instructor;
    }

    /**
     * Sets the storeage path for the validation files
     * @return string
     */
    protected function StoragePath()
    {
        return storage_path('app/public/validations');
    }

    protected function setClassData($key, $data)
    {
        $this->classData[$key] = $data;
    }

    protected function getClassDataByKey($key)
    {
        return $this->classData[$key];
    }

    protected function classroomData()
    {
        return $this->classData;
    }

}
