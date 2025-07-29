<?php

declare(strict_types=1);

namespace App\Models\Traits\CourseAuth;

use DB;

use App\Services\RCache;

use App\Models\User;

use App\Helpers\PgTk;


trait LastInstructor
{

    public function LastInstructor(): User
    {

        $user_id = PgTk::toValue(
            DB::select(
                <<<SQL
SELECT COALESCE( inst_unit.completed_by, inst_unit.created_by )
FROM   inst_unit
WHERE  inst_unit.id = (
	SELECT   student_unit.inst_unit_id
	FROM     student_unit
	JOIN     student_lesson ON student_lesson.student_unit_id = student_unit.id
	WHERE    student_unit.course_auth_id = {$this->id}
	ORDER BY student_lesson.completed_at DESC
	LIMIT 1
)
SQL
            )
        );

        return RCache::Admin($user_id);
    }
}
