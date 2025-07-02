<?php
declare(strict_types=1);

namespace App\Classes\Redirectors;

use Auth;

use App\Classes\Redirectors\RedirectorTrait;


class ExamRedirector
{

    use RedirectorTrait;


    private static $_redirect_route = 'classroom.exam';

    private static $_ignore_routes = [

        // note: these match the _start_ of the route name
        'classroom.exam',

    ];


    public static function handle() : void
    {

        if ( ! self::ShouldContinue() )
        {
            return;
        }

        //
        // ActiveExamAuth() handles expiration
        //

        if ( $ExamAuth = Auth::user()->ActiveExamAuth() )
        {
            self::Redirect( $ExamAuth );
        }

    }

}
