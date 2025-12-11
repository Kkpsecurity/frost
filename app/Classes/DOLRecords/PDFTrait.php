<?php
declare(strict_types=1);

namespace App\Classes\DOLRecords;


trait PDFTrait
{


    protected function Fonts() : self
    {

        $this->_PDF->AddFont( 'Helvetica', '',   'Helvetica.php',               $this->_fontpath );
        $this->_PDF->AddFont( 'Helvetica', 'B',  'Helvetica-Bold.php',          $this->_fontpath );
        #$this->_PDF->AddFont( 'Helvetica', 'BI', 'Helvetica-BoldOblique.php',   $this->_fontpath );
        $this->_PDF->AddFont( 'Helvetica', 'I',  'Helvetica-Oblique.php',       $this->_fontpath );
        #$this->_PDF->AddFont( 'Optima',    '',   'Optima.php',                  $this->_fontpath );
        #$this->_PDF->AddFont( 'Optima',    'B',  'OptimaBold.php',              $this->_fontpath );

        return $this;

    }



    protected function BodyItem( string $title, array $items ) : self
    {

        //
        // title
        //

        $this->_PDF->SetY( $this->_curr_y );

        $this->_PDF->SetFont( 'Helvetica', 'B', 12 );
        $this->_PDF->SetX( $this->_left_x_start );
        $this->_PDF->Cell( $this->_left_x_width, 6, $title, $this->_showborders, 0 );

        //
        // first item
        //

        $item = array_shift( $items );

        $this->_PDF->SetFont( 'Helvetica', '', 12 );
        $this->_PDF->SetX( $this->_right_x_start );
        $this->_PDF->Cell( 0, 6, $item, $this->_showborders, 1 );

        //
        // remaining items
        //

        foreach ( $items as $item )
        {

            $this->_curr_y += 5;
            $this->_PDF->SetY( $this->_curr_y );

            $this->_PDF->SetX( $this->_right_x_start );
            $this->_PDF->Cell( 0, 5, $item, $this->_showborders, 1 );
        }


        $this->_curr_y += 6;

        return $this;

    }


    protected function ExamAdmin() : self
    {

        if ( $ExamAdmin = $this->_CourseAuth->GetExamAdmin() )
        {

            $this->_PDF->SetY( $this->_curr_y );
            $this->_PDF->SetX( $this->_right_x_start );

            $this->_PDF->SetFont( 'Helvetica', 'I', 10 );
            $this->_PDF->Cell( 0, 4, 'Exam access was granted access by Instructor ' . $ExamAdmin, $this->_showborders, 1 );

            $this->_curr_y += 3;

        }

        return $this;

    }

}
