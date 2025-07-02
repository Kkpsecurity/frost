<h3>Student DOL Info</h3>

<form method="post" action="{{ route( 'admin.temp.course_auths.update_student_info', $CourseAuth ) }}" autocomplete="off" >
@csrf

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="fname">First Name</label>
    <div class="col">
      <input type="text" class="form-control" name="fname" id="fname" value="{{ $student_info->fname }}" required />
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="initial">Middle Init.</label>
    <div class="col-sm-2">
      <input type="text" class="form-control" name="initial" id="initial" value="{{ $student_info->initial }}" />
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="lname">Last Name</label>
    <div class="col">
      <input type="text" class="form-control" name="lname" id="lname" value="{{ $student_info->lname }}" required />
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="suffix">Suffix</label>
    <div class="col-sm-2">
      <select class="form-control" name="suffix" id="suffix">
        <option value=""></option>
@foreach ( config( 'define.student_info.suffixes' ) as $suffix )
        {!! Helpers::MakeSelectOpt( $suffix, $suffix, $student_info->suffix ) !!}}
@endforeach
      </select>
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="dob">DOB</label>
    <div class="col-sm-4">
      <input type="text" class="form-control cleave-date" name="dob" id="dob" value="{{ Carbon\Carbon::parse( $student_info->dob )->isoFormat( 'YYYY-MM-DD' ) }}" required />
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="phone">Phone</label>
    <div class="col-sm-4">
      <input type="text" class="form-control" name="phone" id="phone" value="{{ $student_info->phone }}" required />
    </div>
  </div>


  <div class="row mb-3">
    <div class="col-sm-4 offset-sm-3 d-grid">
      <input type="submit" class="form-control btn btn-primary" value="Update" />
    </div>
  </div>

</form>
