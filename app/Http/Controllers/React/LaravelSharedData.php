<?php

namespace App\Http\Controllers\React;

use App\Models\StudentUnit;
use Auth;
use App\RCache;
use App\Models\User;
use App\Models\Validation;
use App\Models\CourseAuth;
use App\Classes\CourseAuthObj;
use App\Classes\ClassroomQueries;
use App\Classes\ValidationsPhotos;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage; 

class LaravelSharedData extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getUserData(CourseAuth $courseAuth)
    {
        if (!$courseAuth->user_id) {
            logger('Empty CourseAuth encountered');
            return null;
        }

        $user = $courseAuth->GetUser();
        $user->avatar = $user->getAvatar('thumb');
        $user->course_auth_id = $courseAuth->id;
        $user->student_unit_id = null;

        $courseAuthObj = new CourseAuthObj($courseAuth);
        $courseDate = $courseAuth->ClassroomCourseDate();

        $validation = "";

        // in this InitStudentUnit  is for the rinstructor not the student 
        // here we are create the student sturctor
        
        $user->studentUnit = null;
        if ($courseDate) {
            if ($studentUnit = StudentUnit::where('course_date_id', $courseDate->id)->where('course_auth_id', $courseAuth->id)->first()) {
                $user->student_unit_id = $studentUnit->id;
                $user->studentUnit = $studentUnit;
                $validation = $studentUnit->verified;
                $user->currentStudentUnit = $studentUnit;

                $user->studentLessons = $studentUnit->StudentLessons();
            }
        }

        $idcardValidation = Validation::where('course_auth_id', $courseAuth->id)->first();
        $headshotValidation = Validation::where('student_unit_id', $user->student_unit_id)->first();

        // Default URL or action if the file doesn't exist
        $defaultUrl = (new Validation())->URL(true); // Assuming this method provides a default URL or path

        $user->validations = [
            'idcard' => $idcardValidation && Storage::disk('public')->exists($idcardValidation->RelPath())
                ? vasset("storage/" . $idcardValidation->RelPath())
                : $defaultUrl,
            'headshot' => $headshotValidation && Storage::disk('public')->exists($headshotValidation->RelPath())
                ? vasset("storage/" . $headshotValidation->RelPath())
                : $defaultUrl,
            'authAgreement' => $courseAuth->agreed_at ? true : false,
            'message' => $validation ?? null,
        ];


        return $user;
    }

    private function getConfigData()
    {
        $config = RCache::SiteConfigsKVP();

        return [
            'zoom' => config('zoom'),
            'agora' => config('agora'),
            'aiagents' => config('aiagents'),
            'site' => $config,
        ];
    }

    public function getLaraData(CourseAuth $courseAuth)
    {
        $user = $this->getUserData($courseAuth);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No User Found',
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings Loaded Successfully',
            'user' => $user,
            'site' => ['base_url' => config('app.url')],
            'config' => $this->getConfigData()
        ], 200);
    }

    public function getLaraAdminData()
    {


        return response()->json([
            'success' => true,
            'message' => 'Settings Loaded Successfully',
            'site' => ['base_url' => config('app.url')],
            'user' => Auth::user(),
            'config' => $this->getConfigData()
        ], 200);
    }

    public function getStudent(CourseAuth $courseAuth)
    {
        $user = $this->getUserData($courseAuth);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No User Found',
            ], 200);
        }

        return response()->json($user);
    }
}