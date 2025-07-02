<?php
declare(strict_types=1);

namespace App\Classes\Certificates;


trait CertificateBodyTrait
{


    public function _MakeCourseTitle() : string
    {

        if ( $this->_use_g28_20h )
        {
            return <<<TEXT
Florida 28 Hour Statewide Firearms "G" License Course
TEXT;
        }

        return $this->_Course->ShortTitle();

    }


    public function _MakeCourseBody() : string
    {

        if ( $this->_use_g28_20h )
        {
            return <<<TEXT
This certificate is awarded for successful completion of 20 hours of
virtual online training toward the 28 hour G license training requirement.
TEXT;
        }


        if ( false !== strpos( $this->_Course->title, 'G28' ) )
        {
            return <<<TEXT
For successful completion of the 28 hours of Armed Security Officer Firearms Training as prescribed by Chapter 493, Florida State Statutes and Rule 5N-1, Florida Administrative Code.
TEXT;
        }

        return <<<TEXT
For successful completion of the 40 hours of Unarmed Security Officer training as prescribed by Chapter 493 Florida State Statutes and Rule 5N-1.140, Florida Administrative Code.
TEXT;

    }


}
