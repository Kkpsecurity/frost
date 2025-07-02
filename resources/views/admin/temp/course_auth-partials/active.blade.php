<h3>
  Active Course Auths
  <span class="float-right">
    <select id="active_course_id" class="form-control">
      <option value="" selected>All Courses</option>
@foreach( RCache::Courses()->where( 'is_active', true ) as $Course )
      <option value="{{ $Course->id }}">{{ $Course->ShortTitle() }}</option>
@endforeach
    </select>
  </span>
</h3>


<table border="0" cellspacing="0" cellpadding="0" class="mb-4">
<tr class="header">
  <td></td>
  <td>Student</td>
  <td align="center">Course</td>
  <td align="center">Started</td>
</tr>

@foreach ( $ActiveCourseAuths as $CourseAuth )
<tr class="active_course_row" data-course_id="{{ $CourseAuth->course_id }}">
  <td>
    <a href="{{ route( 'admin.temp.course_auths.course_auth', $CourseAuth ) }}" class="btn btn-sm btn-primary">View</a>
  </td>
  <td nowrap>{{ $CourseAuth->User->lname }}, {{ $CourseAuth->User->fname }}</td>
  <td nowrap>{{ $CourseAuth->GetCourse()->ShortTitle() }}</td>
  <td nowrap align="right">{{ $CourseAuth->StartDate() }}</td>
</tr>
@endforeach

</table>
