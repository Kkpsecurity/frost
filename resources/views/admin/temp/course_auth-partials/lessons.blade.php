<h3>Course Units / Lessons</h3>

<table border="0" cellspacing="0" cellpadding="0">
@foreach ( $CourseAuth->GetCourse()->GetCourseUnits() as $CourseUnit )

@foreach ( $CourseUnit->Lessons as $Lesson )
<tr>
  <td class="subheader" colspan="6">{{ $CourseUnit }} :: {{ $Lesson }}</td>
</tr>

@forelse ( $StudentLessons->where( 'lesson_id', $Lesson->id ) as $StudentLesson )
<tr>

{{-- StudentLesson Created --}}
  <td>Created</td>
  <td align="center">{{ $StudentLesson->CreatedAt($isofmt) }}</td>

{{-- StudentLesson Completed --}}
  <td>Completed</td>
  <td align="center">
@if ( $StudentLesson->completed_at )
    {{ $StudentLesson->CompletedAt($isofmt) }}
@elseif ( $CourseAuth->StatusObj()->is_active )
    <form method="post" action="{{ route( 'admin.temp.course_auths.mark_lesson_completed', [ $CourseAuth, $StudentLesson ] ) }}">
    @csrf
      <input type="button" class="btn btn-sm btn-warning confirmThis" value="Mark Completed" />
    </form>
@endif
  </td>

{{-- StudentLesson DNC --}}
@if ( $StudentLesson->dnc_at )
  <td>DNC</td>
  <td align="center">
    <form method="post" action="{{ route( 'admin.temp.course_auths.mark_lesson_completed', [ $CourseAuth, $StudentLesson ] ) }}">
    @csrf
      <input type="button" class="btn btn-sm btn-danger confirmThis" value="Mark Completed" />
    </form>
  </td>
@else
  <td colspan="2"></td>
@endif

</tr>
@empty
<tr>
  <td colspan="6" align="center"><i>No Student Lessons</i></td>
</tr>
@endforelse

{{-- end Lesson --}}
@endforeach

<tr><td colspan="6">&nbsp;</td></tr>

{{-- end CourseUnit --}}
@endforeach
</table>
