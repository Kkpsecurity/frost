<?php
namespace App\Http\Controllers\Admin\Frost;

use App\Models\InstUnit;
use App\Models\Traits\InstLesson\GetCourseUnitLesson;
use App\Models\Validation;
use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Models\InstLesson;
use App\Models\StudentUnit;
use App\Models\UserBrowser;
use Illuminate\Http\Request;
use App\Models\StudentLesson;
use App\Classes\CourseAuthObj;
use App\Classes\TrackingQueries;
use App\Traits\PageMetaDataTrait;
use App\Classes\ClassroomQueries;
use App\Http\Controllers\Controller;
use App\Classes\ValidationsPhotos;
use App\Models\Challenge;
use App\Models\CourseUnitLesson;
use Illuminate\Support\Facades\Storage;


class SupportController extends Controller
{

    /**
     * SEO Helper
     */
    use PageMetaDataTrait;

    /**
     * ClassData Object
     */
    public $classData = [];

    /**
     * Class Default Variables:
     */
    protected $defaultData = [];

    /**
     * Pagination Default
     */
    const PAGINATE_DEFAULT = 10;

    public function __construct()
    {
        $this->defaultData = [
            'success' => false,
            'message' => '',

            /**
             * Template Data
             */
            'courses' => null,
            'lessons' => null,
            'courseUnits' => null,

            /**
             * Active Class Data
             */
            'courseDateId' => null,
            'courseUnitId' => null,
            'lessonInProgress' => false,
            'activeLesson' => null,

            /**
             * Student Data
             */
            'student' => null,
            'courseAuths' => null,
            'selectedCourseAuthId' => null,
            'studentUnits' => null,
            'studentLessons' => null,
            'studentActivity' => null,

            /**
             * Instructor Data
             */
            'instructor' => null,
            'instUnit' => null,
            'instUnitLessons' => null,
        ];

        $this->prepareClassData($this->defaultData);
    }

    public function dashboard()
    {
        $content = array_merge([], self::renderPageMeta('support_center'));
        return view('admin.support.dashboard', compact('content'));
    }

    // ##################################################
    // # EndPoints

    public function searchStudents(Request $request)
    {
        $searchInput = $request->input('qsearch');

        $validate = $request->validate([
            'qsearch' => 'required|string',
        ]);

        // Check if the search input is an email
        if (filter_var($searchInput, FILTER_VALIDATE_EMAIL)) {
            // Search by email
            $students = User::where('email', 'ilike', $searchInput)->orderBy('lname')->paginate(self::PAGINATE_DEFAULT);
        } else {
            // Initialize the query with a condition that always fails to seamlessly append OR conditions later
            $students = User::whereRaw('1 = 0');

            // Search for the full name first if the input contains spaces
            if (strpos($searchInput, ' ') !== false) {
                $students = $students->orWhereRaw("concat(fname, ' ', lname) ilike ?", ['%' . $searchInput . '%']);
            } else {
                $searchTerms = explode(' ', $searchInput);
                foreach ($searchTerms as $term) {
                    $students = $students->orWhere('fname', 'ilike', '%' . $term . '%')
                        ->orWhere('lname', 'ilike', '%' . $term . '%');
                }
            }

            $students = $students->orderBy('lname')->paginate(self::PAGINATE_DEFAULT);
        }

        foreach ($students as $key => $student) {
            if (!isset($student->avatar)) {
                $student->avatar = $student->getAvatar('thumb');
                // $student->auths = $student->CourseAuths()->pluck('id')->toArray();
            }
        }

        $content = array_merge([
            'students' => $students,
            'searchInput' => $searchInput,
        ], self::renderPageMeta('support_center'));

        return response()->json($content, 200);
    }

    public function getStudentClassData($student_id)
    {
        /**
         * Check to see if we have CourseDates
         * This will indicate that live Classes are available
         */
        $CourseDates = ClassroomQueries::InstructorDashboardCourseDates();

        /**
         * Initilize the student from the search
         */
        $this->initializeStudentData($student_id, $CourseDates);
        $this->setClassDataFromCourseTemplate(
            $this->getClassDataByKey('courseAuths'),
            $CourseDates
        );

        if ($CourseDates->count() > 0) {
            $this->initializeLiveClassRoom();
        } else {
            $this->initializeOfflineClassRoom();
        }

        $this->setClassData('success', true);
        $this->setClassData('message', 'Student & Classroom Data Fetched Successfully');

        return response()->json($this->classData, 200);
    }

    // ##################################################
    // #  Protected Functions

    protected function setClassData($key, $data)
    {
        $this->classData[$key] = $data;
    }

    protected function getClassDataByKey($key)
    {
        return $this->classData[$key];
    }


    protected function prepareClassData($data)
    {
        foreach ($data as $key => $value) {
            $this->setClassData($key, $value);
        }
    }


    protected function getCurrentCourseId()
    {
        // get from InstUnit to get course id
        $course_id = null;

        $CourseDates = ClassroomQueries::InstructorDashboardCourseDates();

        // get by todays date :: 
        foreach ($CourseDates as $key => $CourseDate) {
            $InstUnit = InstUnit::whereDate('created_at', '>=', Carbon::today()->toDateString())->where('course_date_id', $CourseDate->id)->first();

            if ($InstUnit) {
                $course_id = $InstUnit->GetCourse()->id;
                break;
            }
        }

        return $course_id;
    }

    protected function initializeStudentData($student_id, $CourseDates)
    {
        /**
         * Get the Student 
         */
        $student = User::find($student_id);
        $student->avatar = $student->getAvatar('thumb');

        $course_id = $this->getCurrentCourseId();

        /**
         * Attach courseAuths to the student
         */
        $CourseAuths = $student->courseAuths()->get();
        $this->setClassData('courseAuths', $CourseAuths);

        // get current courseAuth by course_id
        $currentCourseAuth = $CourseAuths->filter(function ($CourseAuth) use ($course_id) {
            return $CourseAuth->course_id === $course_id;
        })->first();

        $this->setClassData('selectedCourseAuthId', $currentCourseAuth ? $currentCourseAuth->id : null);

        /**
         * Get All Student Units based off the courseAuths 
         */
        $studentUnits = StudentUnit::whereIn('course_auth_id', $CourseAuths->pluck('id'))->get();
        $this->setClassData('studentUnits', $studentUnits->toArray());

        /**
         * Get All Student Lessons based off the studentUnits
         */
        $studentLessons = StudentLesson::whereIn('student_unit_id', $studentUnits->pluck('id'))->get();

        // Group lessons by 'student_unit_id'
        $groupedStudentLessons = $studentLessons->groupBy('student_unit_id');

        // Set the grouped lessons as class data
        $this->setClassData('studentLessons', $groupedStudentLessons->toArray());
        
        /**
         * Get the current studentUnit which should base of today date
         */
        $currentStudentUnit = $studentUnits->filter(function ($studentUnit) {
            return Carbon::parse($studentUnit->created_at)->toDateString() === Carbon::today()->toDateString();
        })->first();

        $this->setClassData('currentStudentUnit', $currentStudentUnit);
        $this->setClassData('studentActivity', $this->getStudentActivity($student, $CourseAuths, $student->browser));

        $allStudentLessons = [];
        $todayStudentLessons = [];
        $currentCompletedLessons = [];

        // Assuming $CourseAuths, $studentUnits, $currentStudentUnit, and $student are already defined
        foreach ($CourseAuths as $CourseAuth) {
            // Fetch all student lessons for the units
            $allStudentLessons = StudentLesson::whereIn('student_unit_id', $studentUnits->pluck('id'))->get();

            if (!$allStudentLessons->isEmpty()) {
                $completedLessons = [];

                // Loop over each student lesson
                foreach ($allStudentLessons as $stLesson) {
                    if ($stLesson->completed_at) {
                        $completedLessons[] = $stLesson->lesson_id;
                    }
                }

                // Assign the completed lessons to the corresponding CourseAuth
                $currentCompletedLessons[$CourseAuth->id] = $completedLessons;

                // Fetch today's student lessons if the current student unit is not null
                $todayStudentLessons = [];
                if ($currentStudentUnit !== null) {
                    $todayStudentLessons = StudentLesson::where('student_unit_id', $currentStudentUnit->id)->get();
                }


                $this->setClassData('studentCompletedLessons', $currentCompletedLessons);
                $this->setClassData('todayStudentLessons', $todayStudentLessons);
                $this->setClassData('studentLessons', $allStudentLessons);
            }
        }

        $student = $this->getUpdatedStudentInfo($CourseAuths, $student);
        $this->setClassData('student', $student);
    }

    public function getUpdatedStudentInfo($CourseAuths, $student)
    {
        $validations = [];
        foreach ($CourseAuths as $CourseAuth) {
            foreach (['headshot', 'idcard'] as $type) {
                $validations[$CourseAuth->id][$type] = $this->fetchAndValidateImage($type, $CourseAuth);
            }
        }

        // Assign the validated images and other data to the student
        $student->validations = $validations;

        return $student;
    }

    protected function getStudentActivity($student, $courseAuths, $browser)
    {
        $activity = [];

        foreach ($courseAuths as $key => $CourseAuth) {

            $startDate = $CourseAuth->start_date ? Carbon::parse($CourseAuth->start_date)->isoFormat('MM-DD-YYYY') : "Course Not Started";

            // Calculate 'expires_at' with remaining days
            if ($CourseAuth->expire_date) {
                $expireDate = Carbon::parse($CourseAuth->expire_date);
                // Calculate days remaining; if it's a past date, this will be negative
                $daysRemaining = Carbon::now()->diffInDays($expireDate, false);
                $expiresAtText = $expireDate->isoFormat('MM-DD-YYYY') . " (" . $daysRemaining . " days remaining)";
            } else {
                $expiresAtText = "Pending";
            }

            $activity[$CourseAuth->GetCourse()->id] = [
                "user_id" => $student->id,
                "created_at" => Carbon::parse($CourseAuth->created_at)->isoFormat('MM-DD-YYYY HH:mm:ss'),
                "started_at" => $startDate,
                "agreed_at" => $CourseAuth->agreed_at ? Carbon::parse($CourseAuth->agreed_at)->isoFormat('MM-DD-YYYY HH:mm:ss') : "Student Has Not Agreed",
                "expires_at" => $expiresAtText,
                "disabled_at" => $CourseAuth->disabled_at ? Carbon::parse($CourseAuth->disabled_at)->isoFormat('MM-DD-YYYY HH:mm:ss') : "Active",
                "completed_at" => $CourseAuth->completed_at ? Carbon::parse($CourseAuth->completed_at)->isoFormat('MM-DD-YYYY HH:mm:ss') : "Pending",
                "browser" => $browser->browser ?? null,
            ];
        }

        return $activity;
    }

    protected function initializeLiveClassRoom()
    {
        /**
         * CourseDates are only available during Live Classes
         */
        $CourseDates = ClassroomQueries::InstructorDashboardCourseDates();
        $this->setClassData('courseDates', $CourseDates);

        /**
         * Set the Instructor
         */
        $instructor = $this->setInstructorData($CourseDates);
        $this->setClassData('isClassLive', $instructor ? true : false);

        /**
         * Returns the Current Day Lessons if returned by CourseDate
         */
        $CourseAuths = $this->getClassDataByKey('courseAuths');

        $lessonsMap = [];
        foreach ($CourseAuths as $key => $CourseAuth) {
            $lessonsMap[$CourseAuth->course_id] = $CourseAuth->GetCourse()->GetLessons()->toArray();
        }

        $this->setClassData('lessons', $lessonsMap);


    }

    protected function initializeOfflineClassRoom()
    {
        $CourseAuths = $this->getClassDataByKey('courseAuths');
        $lessonsMap = [];

        foreach ($CourseAuths as $CourseAuth) {
            $lessonsMap[$CourseAuth->course_id] = $CourseAuth->GetCourse()->GetLessons()->toArray();
        }

        $this->setClassData('lessons', $lessonsMap);
        $this->setClassData('isClassLive', false);



    }

    /**
     * Fetches headshots or ID cards based on type and validates the existence of necessary records.
     * 
     * @param string $type Type of the image required ('headshot' or 'idcard')
     * @param CourseAuth $CourseAuth The course authorization object
     * @return string The image or error message
     */
    protected function fetchAndValidateImage($type, $CourseAuth)
    {
        if (!$CourseAuth) {
            return 'Invalid Course Authorization Data';
        }

        $imageOrError = "";
        if ($type == "idcard") {
            $validation = Validation::where('course_auth_id', $CourseAuth->id)->get()->first();

            if ($validation) {
                $idcard = $validation->URL();
            } else {
                $validation = new Validation();
                $idcard = $validation->URL(true);
            }

            $imageOrError = $idcard;
        } elseif ($type == "headshot") {

            /**
             * get student unit for current week from monday top friday
             */
            $studentUnits = StudentUnit::where('course_auth_id', $CourseAuth->id)->where(
                'created_at',
                '>=',
                Carbon::now()->startOfWeek()->toDateTimeString()
            )->get();

            // $studentUnits = StudentUnit::where('course_auth_id', $CourseAuth->id)->get();
            $imageOrError = [];

            if ($studentUnits === null) {
                $imageOrError = "No Head Shots Found";
            } else {
                foreach ($studentUnits as $studentUnit) {
                    $validation = Validation::where('student_unit_id', $studentUnit->id)->get()->first();

                    if ($validation)
                        $headshot = $validation->URL();
                    else {
                        $validation = new Validation();
                        $headshot = $validation->URL(true);
                    }

                    if ($headshot !== false) {
                        $imageOrError[$studentUnit->created_at] = $headshot;
                    }
                }
            }
        }

        return $imageOrError;
    }

    protected function setInstructorData($CourseDates)
    {
        $instructor = null;

        foreach ($CourseDates as $key => $CourseDate) {
            $instructor = $this->assignedInstructor($CourseDate);
            $this->setClassData('instructor', $instructor);
        }

        $this->setClassData('instUnit', $CourseDate->InstUnit);
        $this->setClassData('instUnitLessons', $CourseDate->InstUnit);
        $this->setClassData('instUnitLessonsCOmpleted', $CourseDate->InstUnit ? ClassroomQueries::CompletedInstLessons($CourseDate->InstUnit) : []);

        return $instructor;
    }

    protected function instructStartedDay($CourseDate)
    {
        if ($CourseDate->instUnit) {
            $instructorLesson = ClassroomQueries::ActiveInstLesson($CourseDate->instUnit);
            if ($instructorLesson) {
                $this->setClassData('activeLesson', $instructorLesson->lesson_id);
                $this->setClassData('lessonInProgress', $instructorLesson->lesson_id ? true : false);
            } else {
                $this->setClassData('activeLesson', null);
                $this->setClassData('lessonInProgress', false);
            }
        } else {
            $this->setClassData('activeLesson', null);
            $this->setClassData('lessonInProgress', false);
        }

    }

    protected function assignedInstructor($CourseDate = null)
    {
        $instructor = null;

        $this->instructStartedDay($CourseDate);

        if ($CourseDate && $CourseDate->InstUnit && $CourseDate->InstUnit->created_by) {
            $instructor = $CourseDate->InstUnit->GetCreatedBy();
            $instructor->avatar = $instructor->getAvatar('thumb');

            if (!$instructor->zoom_payload && $zoom = $CourseDate->InstUnit->GetCourse()->ZoomCreds) {
                $zoom->zoom_passcode = \Crypt::decrypt($zoom->zoom_passcode);
                $zoom->zoom_password = \Crypt::decrypt($zoom->zoom_password);
                $instructor->zoom_payload = $zoom->toArray();
            }
        }

        return $instructor;
    }


    protected function setClassDataFromCourseTemplate($CourseAuths, $CourseDates)
    {
        if ($CourseAuths && $CourseAuths->isNotEmpty()) {

            foreach ($CourseAuths as $CourseAuth) {
                $courses[$CourseAuth->GetCourse()->id] = $CourseAuth->GetCourse();

                $courseUnits = $courses[$CourseAuth->GetCourse()->id]->CourseUnits;

                foreach ($courseUnits as $unit) {
                    $courseUnitLessons = $unit->CourseUnitLessons;
                    // $courseUnits[$unit->course_id] = $courseUnitLessons;
                }
            }

            $this->setClassData('courses', $courses);
            $this->setClassData('courseUnits', $courseUnits);
            $this->setClassData('courseUnitLessons', $courseUnitLessons);
        }
    }


    protected function getClassDataKeys($key)
    {
        if (isset($this->classData[$key])) {
            return array_keys($this->classData[$key]);
        }

        return []; // Return an empty array if the key doesn't exist
    }

}
