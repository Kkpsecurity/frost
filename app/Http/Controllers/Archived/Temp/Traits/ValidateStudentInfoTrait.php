<?php

namespace App\Http\Controllers\Admin\Temp\Traits;

use stdClass;
use Illuminate\Support\Carbon;

use App\Models\User;


trait ValidateStudentInfoTrait
{


    private function ValidateStudentInfo( User $User ) : stdClass
    {

        $validated = (object) [
            'fname'     => null,
            'lname'     => null,
            'initial'   => null,
            'suffix'    => null,
            'dob'       => null,
            'phone'     => null,
            'isValid'   => true,
        ];

        $student_info = $User->student_info;


        if ( is_null( $student_info ) )
        {
            $validated->isValid = false;
            return $validated;
        }


        if ( ! array_key_exists( 'fname', $student_info ) )
        {
            $validated->isValid = false;
        }
        else
        {
            $validated->fname = $student_info[ 'fname' ];
        }


        if ( array_key_exists( 'initial', $student_info ) )
        {
            $validated->initial = $student_info[ 'initial' ];
        }
        else if ( array_key_exists( 'initials', $student_info ) )
        {
            $validated->initial = $student_info[ 'initials' ];
        }
        else
        {
            $validated->isValid = false;
        }


        if ( ! array_key_exists( 'lname', $student_info ) )
        {
            $validated->isValid = false;
        }
        else
        {
            $validated->lname = $student_info[ 'lname' ];
        }


        if ( ! array_key_exists( 'suffix', $student_info ) )
        {
            $validated->isValid = false;
        }
        else
        {
            $validated->suffix = $student_info[ 'suffix' ];
        }


        if ( ! array_key_exists( 'dob', $student_info ) )
        {
            $validated->isValid = false;
        }
        else
        {
            $validated->dob = $student_info[ 'dob' ];
        }


        // don't need to validate phone
        $validated->phone = $student_info[ 'phone' ] ?? null;


        return $validated;

    }

}
