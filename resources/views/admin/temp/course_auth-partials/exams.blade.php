<h3>Exams</h3>

<table border="0" cellspacing="0" cellpadding="0">
<tr class="subheader">
  <td align="center">Created</td>
  <td align="center">Completed</td>
  <td align="center">Score</td>
  <td align="center">Passed</td>
  <td align="center">Next Attempt</td>
</tr>

@forelse ( $CourseAuth->ExamAuths as $ExamAuth )
<tr>
  <td align="right">{{ $ExamAuth->CreatedAt( $isofmt ) }}</td>
  <td align="right">{{ $ExamAuth->CompletedAt( $isofmt ) }}</td>
@if ( $ExamAuth->is_passed )
  <td align="center">{{ $ExamAuth->score }}</td>
  <td align="center">Yes</td>
  <td></td>
@else
  <td align="center" style="color: red;">{{ $ExamAuth->score }}</td>
  <td align="center" style="color: red;">@if ( $ExamAuth->completed_at ) No @endif</td>
  <td align="right">{{ $ExamAuth->NextAttemptAt( $isofmt ) }}</td>
@endif
</tr>

@empty
<tr>
  <td colspan="5" align="center"><i>No Exams</i></td>
</tr>
@endforelse

</table>
