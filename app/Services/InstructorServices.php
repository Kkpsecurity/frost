<?php
namespace App\Services;

use App\Models\User;
use App\Classes\ClassroomQueries;

define('INSTRUCTOR_ROLE_ID', 3);

class InstructorServices
{
    private $user;
    
    public function __construct(User $user)
    {
        $this->user = $user->where('role_id', INSTRUCTOR_ROLE_ID)->get();
    }

    public function getAllRegisteredInstructors()
    {
        return $this->user;
    }

    public function validateInstructor($CourseDates)
    {
        $authUser = auth()->user(); 
        if (!$authUser) {
            return false;
        }

       
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

        return $data;
    }
   
}
