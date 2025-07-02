<?php
declare(strict_types=1);

namespace App\Classes\DOLRecords;

#use Auth;
use Str;

use Fpdf\Fpdf;

use App\Classes\DOLRecords\HelpersTrait;
use App\Classes\DOLRecords\LessonsTrait;
use App\Classes\DOLRecords\PDFTrait;
use App\Classes\DOLRecords\StorageTrait;

#use RCache;
use App\Models\CourseAuth;


class DOLRecordPDF
{

    use HelpersTrait, LessonsTrait, PDFTrait, StorageTrait;


    protected $_CourseAuth;
    protected $_Course;
    protected $_User;

    protected $_PDF;
    protected $_fontpath = 'app/pdfcerts/fonts/';


    protected $_left_x_start    = 10;
    protected $_left_x_width    = 20;
    protected $_right_x_start   = 30;
    protected $_curr_y          = 5;
    protected $_showborders     = 0;  // debugging


    // __construct() is in StorageTrait


    //
    //
    //


    public function GenPDF( int $course_auth_id ) : string
    {

        $this->_CourseAuth = CourseAuth::findOrFail( $course_auth_id );
        $this->_Course     = $this->_CourseAuth->GetCourse();
        $this->_User       = $this->_CourseAuth->GetUser();


        //
        // init PDF
        //


        $this->_PDF = new Fpdf();

        $this->_PDF->AddPage( 'P', 'Letter' );
        $this->_PDF->SetAutoPageBreak( 1, 5 );

        $this->Fonts();


        //
        // add content
        //


        $this->BodyItem( 'Student', [ $this->_User->fullname(), $this->_User->email ] )
             ->BodyItem( 'Course',  [ $this->_Course->LongTitle() ] )
             ->BodyItem( 'Status',  [ $this->CourseStatus() ] );

        if ( $exam_auths = $this->ExamAuths() )
        {
            $this->BodyItem( 'Exams', $exam_auths )
                 ->ExamAdmin();
        }

        $this->CourseUnitLessons();


        //
        // save PDF
        //


        $this->_PDF->Output( 'F', $this->FileName() );
        return "Wrote {$this->FileName()}";


    }


}
