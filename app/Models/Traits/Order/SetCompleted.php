<?php

declare(strict_types=1);

namespace App\Models\Traits\Order;

use App\Models\CourseAuth;
use App\Events\Enrollment\CourseEnrolled;


trait SetCompleted
{


    public function SetCompleted(): void
    {

        if ($this->completed_at) {
            logger("OrderID {$this->id} already completed.");
            return;
        }

        if (! $this->course_auth_id) {

            $CourseAuth = CourseAuth::create([

                'user_id'   => $this->user_id,
                'course_id' => $this->course_id,

            ]);

            $this->course_auth_id = $CourseAuth->id;

            event(new CourseEnrolled($CourseAuth));
        }

        $this->completed_at = $this->freshTimestamp();
        $this->save();
        $this->refresh();
    }
}
