@extends('layouts.admin')


@section('content')
@include( 'admin.temp.partials.messages' )


  <div class="tablewrapper">

    <table border="1" cellspacing="0" cellpadding="0">
    <tr class="header">
      <td align="center">ID</td>
      <td nowrap>Course</td>
      <td nowrap>Exam Questions</td>
      <td nowrap>Required To Pass</td>
    </tr>

@foreach ( RCache::Exams() as $Exam )
<tr>
  <td align="center">{{ $Exam->id }}</td>
  <td>{{ $Exam->admin_title }}</td>
  <td align="center">{{ $Exam->num_questions }}</td>
  <td align="center">{{ $Exam->num_to_pass }}</td>
</tr>
@endforeach

    </table>
  </div>


@include( 'admin.temp.partials.asset-loader' )
@endsection
