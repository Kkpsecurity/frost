<?php

declare(strict_types=1);

namespace App\Models\Traits\Order;

use App\Models\CourseAuth;
use App\Events\Enrollment\CourseEnrolled;
use Illuminate\Support\Facades\DB;


trait SetCompleted
{


    public function SetCompleted(): void
    {

        if ($this->completed_at) {
            logger("OrderID {$this->id} already completed.");
            return;
        }

        // Wrap in a transaction so CourseAuth creation and Order update
        // are atomic. If Order::save() fails (e.g. Redis down), the
        // CourseAuth row is rolled back — no orphaned course_auths.
        // NOTE: events are fired AFTER the transaction to avoid a
        // notification failure rolling back the committed records.
        $courseAuth = null;

        DB::transaction(function () use (&$courseAuth) {

            if (! $this->course_auth_id) {

                $courseAuth = CourseAuth::create([
                    'user_id'   => $this->user_id,
                    'course_id' => $this->course_id,
                ]);

                $this->course_auth_id = $courseAuth->id;
            }

            $this->completed_at = $this->freshTimestamp();

            // Use direct DB update to avoid Redis-dependent Observable trait
            DB::table('orders')->where('id', $this->id)->update([
                'course_auth_id' => $this->course_auth_id,
                'completed_at'   => $this->completed_at,
                'updated_at'     => $this->freshTimestamp(),
            ]);

            $this->refresh();
        });

        // Fire enrollment event outside the transaction so that a
        // notification failure (e.g. browser push not configured) does
        // not roll back the already-committed CourseAuth / Order rows.
        if ($courseAuth) {
            event(new CourseEnrolled($courseAuth));
        }
    }
}
