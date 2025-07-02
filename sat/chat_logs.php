<?php

use Illuminate\Support\Carbon;

use App\RCache;
use App\Classes\ChatLogCache;
use App\Helpers\DevHelpers;
use App\Models\ChatLog;
use App\Models\CourseDate;
use App\Models\User;


$CourseDate = DevHelpers::CurrentCourseDate();

ResetAll( $CourseDate );      print "<br />\n";
TestEnable( $CourseDate );    print "<br />\n";
CreateRecords( $CourseDate );



$html =<<<HTML
<h3>ChatLogs</h3>
<table border="1" cellspacing="0" cellpadding="3">
<tr>
  <th>IDX</th>
  <th>ID</th>
  <th>Created At</th>
  <th>Student</th>
  <th>Instructor</th>
</tr>

HTML;


$idx = 0;
foreach ( ChatLogCache::Query( $CourseDate->id ) as $ChatLog )
{

    $created_at = ( new Carbon( $ChatLog->created_at ) )
                          ->tz( 'America/New_York' )
                   ->isoFormat( 'ddd MM/DD HH:mm:ss' );

    $html .=<<<ROW
<tr>
  <td align="center">{$idx}</td>
  <td align="center">{$ChatLog->id}</td>
  <td>{$created_at}</td>
  <td>{$ChatLog->GetStudent()}</td>
  <td>{$ChatLog->GetInst()}</td>
</tr>

ROW;

    $idx++;

}


print $html . "</table>\n";
exit();


#
#
#


function CreateRecords( CourseDate $CourseDate )
{

    $max      = 150;
    $inserted = 0;

    $Instructors = RCache::Admins();
    $Students    = User::where( 'role_id', 4 )->get();

    foreach ( range( 1, $max ) as $idx )
    {

        if ( rand( 0, 10 ) == 1 )
        {
            $inst_id    = $Instructors->random()->id;
            $student_id = null;
        }
        else
        {
            $inst_id    = null;
            $student_id = $Students->random()->id;
        }

        ChatLog::create([

            'course_date_id'    => $CourseDate->id,
            'inst_id'           => $inst_id,
            'student_id'        => $student_id,
            'body'              => 'asdf 1234',
            'hidden_at'         => ( rand( 0, 25 ) == 1 ? Carbon::now() : null ),

        ]);

        $inserted++;

    }

    print "Created {$inserted} records\n";

}



function TestEnable( CourseDate $CourseDate )
{


    print 'Enabling ChatLog: ';
    ChatLogCache::Enable( $CourseDate->id );
    if ( ! ChatLogCache::IsEnabled( $CourseDate->id ) )
    {
        print "<span style=\"color: red; font-weight: bold;\">Error Enabling ChatLog</span><br />\n";
    }
    else
    {
        print "OK<br />\n";
    }

    print 'Disabling ChatLog: ';
    ChatLogCache::Disable( $CourseDate->id );
    if ( ChatLogCache::IsEnabled( $CourseDate->id ) )
    {
        print "<span style=\"color: red; font-weight: bold;\">Error Disabling ChatLog</span><br />\n";
    }
    else
    {
        print "OK<br />\n";
    }

}


function ResetAll( CourseDate $CourseDate )
{

    ChatLogCache::Redis()->del( ChatLogCache::RedisKey( $CourseDate->id ) );
    print "Deleted ChatLog cache<br />\n";

    ChatLogCache::Disable( $CourseDate->id );
    print "Disabled ChatLog<br />\n";

    ChatLog::truncate();
    print "Truncated ChatLog<br />\n";

}
