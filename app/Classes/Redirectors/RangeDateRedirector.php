<?php

declare(strict_types=1);

namespace App\Classes\Redirectors;

use Illuminate\Support\Facades\Auth;

use App\Traits\RedirectorTrait;


class RangeDateRedirector
{

    use RedirectorTrait;


    private static $_redirect_route = 'range_date.select';

    private static $_ignore_routes = [
        // note: these match the _start_ of the route name
        'range_date',
        'classroom.exam',
    ];


    public static function handle(): void
    {

        if (! self::ShouldContinue()) {
            return;
        }

        foreach (Auth::user()->InactiveCourseAuths->whereNull('disabled_at') as $CourseAuth) {
            if ($CourseAuth->GetCourse()->needs_range && ! $CourseAuth->range_date_id) {
                self::Redirect($CourseAuth);
            }
        }
    }
}
