<?php
declare(strict_types=1);

namespace App\Classes\Certificates;

use Auth;

use Fpdf\Fpdf;

use App\Classes\Certificates\CertificateBodyTrait;
use App\Classes\Certificates\CertificateHelpersTrait;
use App\Models\CourseAuth;


class CertificatePDF
{

    use CertificateBodyTrait, CertificateHelpersTrait;


    protected $_CourseAuth;
    protected $_Course;
    protected $_User;

    protected $_use_g28_20h;

    protected $_PDF;

    // these get converted later
    protected $_fontpath        = 'app/pdfcerts/fonts/';
    protected $_imgpath         = 'app/pdfcerts/images/';

    protected $_background_img  = 'stg_certificate_border.png';


    // positioning

    public $y_header        = 42;

    public $y_awarded_to    = 62;
    public $y_student_name  = 70;

    public $y_class_name    = 90;
    public $y_class_desc    = 104;

    public $y_exam_score    = 118;

    public $x_awarded_on    = 32;
    public $x_date          = 70;
    public $y_date          = 135;
    public $y_license       = 144;  // y_date + 9

    public $x_inst_name     = 170;
    public $y_inst_name     = 150;
    public $y_inst_info     = 156;  // y_inst_name + 6

    public $x_inst_sig      = 160;  // x_inst_name - 10
    public $y_inst_sig      = 130;
    #public $w_inst_sig      = 80;
    #public $h_inst_sig      = 32;
    public $w_inst_sig      = 70;
    public $h_inst_sig      = 28;

    protected $_showborders = 0; // debugging



    public function G20HourPDF( CourseAuth $CourseAuth )
    {
        return $this->CertificatePDF( $CourseAuth, true );
    }


    public function CertificatePDF( CourseAuth $CourseAuth, bool $use_g28_20h = false )
    {

        $this->_CourseAuth  = $CourseAuth;
        $this->_Course      = $CourseAuth->GetCourse();
        $this->_User        = $CourseAuth->GetUser();

        $this->_use_g28_20h = $use_g28_20h;


        if ( $error = $this->_ValidCourseAuth() )
        {
            return back()->with( 'error', $error );
        }


        //
        // prep
        //

        $this->_LoadDefines();


        //
        // generate Fpdf object
        //

        $this->_PDF = new Fpdf();

        $this->_PDF->AddPage( 'L', 'Letter' );


        //
        // load components
        //

        $this->_Fonts()
             ->_BackgroundImage()
             ->_Header()
             ->_StudentName()
             ->_CourseTitle()
             ->_CourseBody()
             ->_CertDate()
             ->_Instructor();

        if ( $this->_use_g28_20h )
        {
            $this->_ExamScore();
        }
        else
        {
             $this->_DSLicense();
        }

        //
        // send it
        //

        no_cache_headers();
        header( 'Content-Type: application/pdf' );
        $this->_PDF->Output( 'D', $this->_CourseAuth->id . '.pdf' );

    }



    protected function _BackgroundImage() : self
    {

        $this->_PDF->Image( $this->_imgpath . $this->_background_img, 0, 0, 279, 215, 'PNG' );

        return $this;

    }



    protected function _Fonts() : self
    {

        $this->_PDF->AddFont( 'Helvetica', '',   'Helvetica.php',               $this->_fontpath );
        $this->_PDF->AddFont( 'Helvetica', 'B',  'Helvetica-Bold.php',          $this->_fontpath );
        $this->_PDF->AddFont( 'Helvetica', 'BI', 'Helvetica-BoldOblique.php',   $this->_fontpath );
        $this->_PDF->AddFont( 'Helvetica', 'I',  'Helvetica-Oblique.php',       $this->_fontpath );
        $this->_PDF->AddFont( 'Optima',    '',   'Optima.php',                  $this->_fontpath );
        $this->_PDF->AddFont( 'Optima',    'B',  'OptimaBold.php',              $this->_fontpath );

        return $this;

    }


    protected function _Header() : self
    {

        $this->_PDF->SetFont( 'Helvetica', 'I', 42 );
        $this->_PDF->SetY( $this->y_header );
        $this->_PDF->Cell( 0, 14, 'Certificate of Completion', $this->_showborders, 1, 'C' );

        return $this;

    }


    protected function _StudentName() : self
    {

        $this->_PDF->SetFont( 'Optima', 'B', 18 );
        $this->_PDF->SetY( $this->y_awarded_to );
        $this->_PDF->Cell( 0, 8, 'Awarded to:', $this->_showborders, 1, 'C' );

        $this->_PDF->SetFont( 'Helvetica', 'B', 36 );
        $this->_PDF->SetY( $this->y_student_name );
        $this->_PDF->Cell( 0, 14, $this->_User->fullname(), $this->_showborders, 1, 'C' );

        return $this;

    }


    protected function _CourseTitle() : self
    {

        $font_size = $this->_use_g28_20h ? 20 : 24;

        $this->_PDF->SetFont( 'Helvetica', 'B', $font_size );
        $this->_PDF->SetY( $this->y_class_name );
        $this->_PDF->Cell( 0, 10, $this->_MakeCourseTitle(), $this->_showborders, 1, 'C' );

        return $this;

    }



    protected function _CourseBody() : self
    {

        $info_w = 170;
        $info_x = ( 280 - $info_w ) / 2;

        $this->_PDF->SetFont( 'Helvetica', '', 14 );
        $this->_PDF->SetXY( $info_x, $this->y_class_desc );
        $this->_PDF->MultiCell( $info_w, 6, $this->_MakeCourseBody(), $this->_showborders, 'C' );

        return $this;

    }


    protected function _CertDate() : self
    {


        $cert_date = $this->_CourseAuth->CompletedAt( 'YYYY-MM-DD' );

        if ( Auth::id() == 1 && ! $cert_date )
        {
            $cert_date = date( 'Y-m-d' );
        }

        $this->_PDF->SetFont( 'Optima', 'B', 18 );
        $this->_PDF->SetXY( $this->x_awarded_on, $this->y_date );
        $this->_PDF->Cell( 38, 10, 'Awarded on:', $this->_showborders );

        $this->_PDF->SetFont( 'Helvetica', 'B', 20 );
        $this->_PDF->SetXY( $this->x_date, $this->y_date );
        $this->_PDF->Cell( 40, 11, $cert_date, $this->_showborders );

        return $this;

    }


    protected function _DSLicense() : self
    {

        $this->_PDF->SetFont( 'Helvetica', '', 12 );
        $this->_PDF->SetXY( $this->x_date, $this->y_license );
        $this->_PDF->Cell( 40, 6, config( 'define.licenses.STG.DS' ), $this->_showborders );

        return $this;

    }



    protected function _Instructor() : self
    {

        $Instructor  = $this->_FinalInstructor();
        $instsigfile = $this->_InstructorSigFile( $Instructor );


        //
        // instructor signature
        //

        $this->_PDF->Image( $instsigfile, $this->x_inst_sig, $this->y_inst_sig, $this->w_inst_sig, $this->h_inst_sig, 'PNG' );


        //
        // instructor name
        //

        $this->_PDF->SetFont( 'Helvetica', 'B', 12 );
        $this->_PDF->SetXY( $this->x_inst_name, $this->y_inst_name );
        $this->_PDF->Cell( 60, 6, $Instructor->fullname(), $this->_showborders );


        //
        // instructor licenses
        //

        if ( $inst_licenses = join( "\n", $Instructor->InstLicenses->pluck( 'license' )->toArray() ) )
        {
            $this->_PDF->SetFont( 'Helvetica', '', 11 );
            $this->_PDF->SetXY( $this->x_inst_name, $this->y_inst_info );
            $this->_PDF->MultiCell( 60, 5, $inst_licenses, $this->_showborders );
        }


        return $this;

    }


    protected function _ExamScore() : self
    {

        if ( Auth::id() == 1 && ! $this->_CourseAuth->LatestExamAuth )
        {
            $score = '48 / 50';
        }
        else
        {
            $score = $this->_CourseAuth->LatestExamAuth->score;
        }

        list( $correct, $total ) = explode( ' / ' , $score );
        $percent = intval( $correct / $total * 100 );

        $this->_PDF->SetFont( 'Helvetica', 'B', 12 );
        $this->_PDF->SetY( $this->y_exam_score );
        $this->_PDF->Cell( 0, 10, "Exam: {$percent}%", $this->_showborders, 1, 'C' );

        return $this;

    }


}
