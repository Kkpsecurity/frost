@extends('layouts.admin')


@section('content')
@include( 'admin.temp.partials.messages' )


  <div class="tablewrapper">

    <a href="{{ route( 'admin.temp.completed_course_auths' ) }}" class="btn btn-secondary navBackBtn">Return to List</a>

    <div class="grid_table">

      <div>Course</div>
      <div class="text-danger">{{ $CourseAuth->GetCourse()->ShortTitle() }}</div>
      <div>Completed</div>
      <div class="copy_to_clipboard">{{ Carbon\Carbon::parse( $CourseAuth->completed_at )->tz( 'America/New_York' )->isoFormat( 'MMDDYYYY' ) }}</div>
@if ( $CourseAuth->GetCourse()->needs_range )
      <div>Exam Score</div>
      <div>
        <span class="copy_to_clipboard">{{ $ExamAuth->score }}</span>
        &nbsp;
        <span class="copy_to_clipboard">{{ $ExamAuth->ScorePercent() }}</span>%
      </div>
@endif

      <div class="grid-spacer"></div>

@if ( ! $student_info->isValid )
      <div class="grid-span-all bg-danger text-center px-2 py-1">
        Error: Incomplete Student Info
      </div>
      <div class="grid-span-all text-center py-1">
        <a href="{{ route( 'admin.temp.course_auths.course_auth', $CourseAuth ) }}" target="_blank">Update Student Info</a>
      </div>
@else
      <div>First Name</div>
      <div class="copy_to_clipboard">{{ $student_info->fname }}</div>
      <div>Middle</div>
      <div class="copy_to_clipboard">{{ $student_info->initial }}</div>
      <div>Last Name</div>
      <div class="copy_to_clipboard">{{ $student_info->lname }}</div>
      <div>Suffix</div>
      <div class="copy_to_clipboard">{{ $student_info->suffix }}</div>
      <div>DOB</div>
      <div class="copy_to_clipboard">{{ Carbon\Carbon::parse( $student_info->dob )->isoFormat( 'MMDDYYYY' ) }}</div>
@endif

      <div class="grid-spacer"></div>

      <div>Instructor</div>
      <div>{{ $Instructor }}</div>
      <div>Licenses</div>
      <div>
@foreach ( $Instructor->InstLicenses->pluck( 'license' )->sort()->toArray() as $license )
        <div class="copy_to_clipboard">{{ $license }}</div>
@endforeach
      </div>

      <div class="grid-spacer"></div>

      <div class="grid-span-2">
      <form method="post" action="{{ route( 'admin.temp.completed_course_auths.update', $CourseAuth ) }}" autocomplete="off">
      @csrf
        <div class="input-group">
          <input type="text" class="form-control" name="dol_tracking" id="dol_tracking" placeholder="DOL Tracking #" required />
          <input type="submit" class="btn btn-primary" value="Submit" />
        </div>
      </form>
      </div>

      <div class="grid-span-2 mt-3">
        <div class="copy_to_clipboard">{{ $User->fullname() }}</div>
        <a class="emaillink" href="mailto:{{ $User->email }}">{{ $User->email }}</a>
      </div>

      <div class="grid-span-2 text-center mt-4 mb-3">
        <a href="{{ route( 'admin.temp.course_auths.course_auth', $CourseAuth ) }}" class="btn btn-info" target="_blank">Update Student DOL Info</a>
      </div>

    </div>

  </div>


@include( 'admin.temp.partials.asset-loader' )

<style>
.copy_to_clipboard
{
    padding: 2px 6px;
    border:  1px solid #ccc;
}
#dol_tracking { width:    180px; }
.emaillink    { font-size: 16px; }
</style>

@endsection
