<?php

namespace App\Http\Controllers\Student;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;

use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Models\CourseUnit;
use App\Models\StudentUnit;
use App\Models\StudentActivity;
use App\Models\User;
use App\Classes\ClassroomQueries;
use App\Traits\PageMetaDataTrait;
use App\Http\Controllers\Controller;

use App\Services\AttendanceService;
use App\Services\IdVerificationService;
use App\Services\StudentActivityTracker;
use App\Services\StudentDashboardService;
use App\Services\StudentDataArrayService;
use App\Services\ClassroomDataArrayService;
use App\Services\ClassroomDashboardService;
use App\Services\StudentUnitService;
use App\Services\SelfStudyLessonService;

// New refactored services
use App\Services\Student\StudentAttendanceService;
use App\Services\Student\StudentVerificationService;
use App\Services\Student\StudentLessonService;
use App\Services\Student\StudentClassroomService;
use App\Traits\StudentDataHelper


class StudentDashboardController extends Controller {} 
