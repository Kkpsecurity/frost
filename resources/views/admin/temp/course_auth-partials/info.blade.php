<?php

$selected_admin_id = Auth::user()->role_id == RCache::RoleID( 'Instructor' ) ? Auth::id() : 10;

?>
<div class="grid_table">

  <div>CourseAuth ID</div>
  <div>{{ $CourseAuth->id }}</div>

  <div>Student</div>
  <div>{{ $CourseAuth->GetUser() }}</div>

  <div>Course</div>
  <div>{{ $CourseAuth->GetCourse()->ShortTitle() }}</div>

  <div>Created At</div>
  <div>{{ $CourseAuth->CreatedAt( $isofmt ) }}</div>

  <div>Completed At</div>
  <div>{{ $CourseAuth->CompletedAt( $isofmt ) }}</div>

  <div>Status</div>
  <div>{!! $CourseAuth->StatusObj()->html !!}</div>

@if ( $CourseAuth->exam_admin_id )

  <div>ExamAdmin</div>
  <div>{{ $CourseAuth->GetExamAdmin() }}</div>

@elseif ( $CourseAuth->StatusObj()->is_active && ! $CourseAuth->AllLessonsCompleted() )

  <div>ExamAdmin</div>
  <div>
    <form method="post" action="{{ route( 'admin.temp.course_auths.mark_admin_exam_auth', $CourseAuth ) }}">
    @csrf
    <div class="d-grid">
      <select class="form-select form-select-sm" name="admin_user_id">
@foreach ( RCache::Admins()->where( 'is_active', true ) as $Admin )
        <option value="{{ $Admin->id }}" @if ( $Admin->id == $selected_admin_id ) selected @endif>{{ $Admin }}</option>
@endforeach
      </select>
      <br />
      <input type="button" class="btn btn-sm btn-danger confirmThis" value="Admin Authorize Exam" />
    </div>
    </form>
  </div>

@endif

</div>
