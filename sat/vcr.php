<?php

use Illuminate\Support\Carbon;

use App\Classes\VideoCallRequest;
use App\Helpers\DevHelpers;
use App\Models\CourseDate;
use App\Models\User;


function vcr_display()
{

    if ( ! $CourseDate = DevHelpers::CurrentCourseDate() )
    {
        return vcr_header() . '<h3>No active CourseDate</h3>' . vcr_footer();
    }


    return vcr_header( $CourseDate )
         . vcr_accepted( $CourseDate )
         . vcr_queue( $CourseDate )
         . vcr_footer();


}


########################################


function vcr_accepted( CourseDate $CourseDate )
{

    $devdata = VideoCallRequest::DevData( $CourseDate->id );

    if ( is_numeric( $devdata->inst_user_id ) )
    {
        $inst_route  = route( 'sattest.vcr.call_cancel_instructor', $CourseDate->id );
        $inst_button = '<input type="submit" value="Delete">';
    }
    else
    {
        $inst_route  = null;
        $inst_button = null;
    }

    if ( is_numeric( $devdata->student_user_id ) )
    {
        $student_route  = route( 'sattest.vcr.call_cancel_student', [ $CourseDate->id, $devdata->student_user_id ] );
        $student_button = '<input type="submit" value="Delete">';
    }
    else
    {
        $student_route  = null;
        $student_button = null;
    }


    $csrf_field = vcr_csrf_field();

    return <<<HTML

<div style="margin-top: 20px; display: table; border: 2px solid #ccc; padding: 10px;">
<table border="0" cellspacing="0" cellpadding="5">
<tr>
  <th>Accepted By</th>
  <th>User ID</th>
  <th>Redis Key</th>
  <th>&nbsp;</th>
</tr>
<tr>
  <td align="center">Instructor</td>
  <td align="center">{$devdata->inst_user_id}</td>
  <td>{$devdata->inst_redis_key}</td>
  <td>
    <form method="post" action="{$inst_route}">
      {$csrf_field}
      {$inst_button}
    </form>
  </td>
</tr>
<tr>
  <td align="center">Student</td>
  <td align="center">{$devdata->student_user_id}</td>
  <td>{$devdata->student_redis_key}</td>
  <td>
    <form method="post" action="{$student_route}">
      {$csrf_field}
      {$student_button}
    </form>
  </td>
</tr>
</table>
</div>

HTML;

}


########################################


function vcr_queue( CourseDate $CourseDate )
{

    $records = VideoCallRequest::Queue( $CourseDate->id );

    if ( ! $records->count() )
    {
        return "\n<h4>Queue is empty</h4>\n";
    }


    $html =<<<HTML

<div style="margin-top: 20px; display: table; border: 2px solid #ccc; padding: 10px;">
<table border="0" cellspacing="0" cellpadding="5">
<tr>
  <th align="left">Created At</th>
  <th>UserID</th>
  <th align="left">Name</th>
  <th align="left">Email</th>
  <th>&nbsp;</th>
</tr>
HTML;


    $csrf_field = vcr_csrf_field();

    foreach ( $records as $record )
    {

        $created_at = ( new Carbon( $record['created_at'] ) )
                              ->tz( 'America/New_York' )
                       ->isoFormat( 'ddd MM/DD HH:mm:ss' );

        $btn_route = route( 'sattest.vcr.call_delete_all', [ $CourseDate->id, $record['user_id'] ] );

        $html .=<<<ROW
<tr>
  <td>{$created_at}</td>
  <td align="center">{$record['user_id']}</td>
  <td>{$record['fname']} {$record['lname']}</td>
  <td>{$record['email']}</td>
  <td>
    <form method="post" action="{$btn_route}">
      {$csrf_field}
      <input type="submit" value="Delete">
    </form>
  </td>
</tr>

ROW;
    }


    $html .=<<<HTML
</table>
</div>

HTML;


    return $html;

}


########################################


function vcr_csrf_field()
{
    return '<input type="hidden" name="_token" value="' .  csrf_token() . '">';
}


function vcr_header( CourseDate $CourseDate = null )
{

    $title = 'Video Call Requests - Redis';

    $html =<<<HTML
<!doctype html>
<html lang="en">
<head>

  <title>{$title}</title>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto+Mono" />
  <link rel="stylesheet" type="text/css" href="/sat/sat.css" />

</head>
<body>

HTML;


    if ( $CourseDate )
    {

        $html .= <<<HTML

<h3>
  {$title}
  <button type="button" onClick="location.reload();" style="margin-left: 20px; padding: 5px 20px; font-size: 18px;">Refresh</button>
</h3>

<p>CourseDateID: {$CourseDate->id} &bull; {$CourseDate->StartsAt('ddd MM/DD')}</p>

HTML;

    }


    return $html;

}


function vcr_footer()
{
    return <<<HTML

</body>
</html>
HTML;
}
