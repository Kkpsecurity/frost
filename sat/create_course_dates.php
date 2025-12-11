<?php

use Illuminate\Support\Carbon;


function create_course_dates()
{


    $create_weeks = 4;

    $begin_date = Carbon::now()->startOfWeek();


    //
    // prepare table
    //

    $html = <<<HTML

<style>
body, tr, td, th
{
    font-size:   15px;
    font-family: arial, helvetica, sans-serif;
}
</style>

<table border="0" cellspacing="3" cellpadding="4">

HTML;


    foreach ( RCache::Courses() as $Course )
    {

        if ( ! $Course->is_active or ! $Course->dates_template ) continue;


        $html .= <<<ROW
<tr>
  <td colspan="10" style="padding: 10px; font-weight: bold; background-color: #ddd;">
    {$Course->title_long}
  </td>
</tr>

ROW;


        foreach ( range( 0, $create_weeks-1 ) as $week )
        {

            $html .= "<tr><td colspan=\"10\">Week: {$week}</td></tr>\n";

            // first day of this week
            $first_of_week = ( clone $begin_date )->addWeeks( $week );


            foreach ( $Course->dates_template['week_1'] as $template_day )
            {

                $CourseUnit = RCache::CourseUnits( $template_day['course_unit_id'] );

                list( $start_hour, $start_min ) = explode( ':', $template_day['start'] );
                list( $end_hour,   $end_min   ) = explode( ':', $template_day['end'] );

                $starts_at = ( clone $first_of_week )->timezone( 'America/New_York' )->addDays( $template_day['wday'] )->hour( $start_hour )->minute( $start_min );
                $ends_at   = ( clone $first_of_week )->timezone( 'America/New_York' )->addDays( $template_day['wday'] )->hour( $end_hour   )->minute( $end_min   );

                $starts_at = $starts_at->isoFormat( 'ddd MM/DD HH:mm' );
                $ends_at   = $ends_at->isoFormat( 'ddd MM/DD HH:mm' );

                $html .= <<<ROW
<tr>
  <td>{$CourseUnit->admin_title}</td>
  <td align="right">{$starts_at}</td>
  <td align="right">{$ends_at}</td>
</tr>

ROW;


            } // end template_day

        } // end week


        $html .= "<tr><td colspan=\"10\">&nbsp;</td></tr>\n";

    } // end Course



    //
    // end table
    //

    return "{$html}</table>\n";

}
