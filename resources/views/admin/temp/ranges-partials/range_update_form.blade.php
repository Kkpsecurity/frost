<form method="post" action="{{ route( 'admin.temp.ranges.update', $Range ) }}" autocomplete="off" >
@csrf

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="name">Range Name</label>
    <div class="col">
      <input type="text" class="form-control" name="name" id="name" value="{{ $Range->name }}" required />
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="city">City</label>
    <div class="col">
      <input type="text" class="form-control" name="city" id="city" value="{{ $Range->city }}" required />
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="address">Address</label>
    <div class="col">
      <textarea class="form-control" name="address" id="address" rows="2" required >{{ $Range->address }}</textarea>
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="inst_name">Instructor</label>
    <div class="col">
      <input type="text" class="form-control" name="inst_name" id="inst_name" value="{{ $Range->inst_name }}" required />
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="inst_email">Email</label>
    <div class="col">
      <input type="email" class="form-control" name="inst_email" id="inst_email" value="{{ $Range->inst_email }}" />
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="inst_phone">Phone</label>
    <div class="col-sm-3">
      <input type="text" class="form-control" name="inst_phone" id="inst_phone" value="{{ $Range->inst_phone }}" placeholder="(xxx) xxx-xxxx" />
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-sm-3 offset-sm-3 d-grid">
      <input type="submit" class="form-control btn btn-primary" value="Update" />
    </div>
</form>


{{--
    <div class="col-sm-4">
      <form method="post" action="{{ route( 'admin.temp.ranges.range.toggleactive', $Range ) }}" >
      @csrf
@if ( $Range->is_active )
        <input type="submit" class="form-control btn btn-danger" value="Deactivate Range" />
@else
        <input type="submit" class="form-control btn btn-warning" value="Reactivate Range" />
@endif
      </form>
    </div>
--}}

  </div>



@if ( ! $Range->appt_only )
<form method="post" action="{{ route( 'admin.temp.ranges.update.times', $Range ) }}" autocomplete="off" >
@csrf

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="price">Times</label>
    <div class="col-sm-3 mb-1">
      <input type="text" class="form-control" name="times" id="times" value="{{ $Range->times }}" />
    </div>
    <div class="col-sm-4 mb-1">
      <input type="submit" class="form-control btn btn-warning" value="Update Future Dates" />
    </div>
  </div>

</form>
@else
  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="times">Times</label>
    <div class="col col-form-label">
      <em>By Appointment Only</em>
    </div>
  </div>
@endif


<form method="post" action="{{ route( 'admin.temp.ranges.update.price', $Range ) }}" autocomplete="off" >
@csrf

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="price">Price</label>
    <div class="col-sm-3 mb-1">
      <input type="text" class="form-control cleave-price" name="price" id="price" value="{{ $Range->price }}" />
    </div>
    <div class="col-sm-4 mb-1">
      <input type="submit" class="form-control btn btn-warning" value="Update Future Dates" />
    </div>
  </div>

</form>


  <div class="row mb-3">

    <div class="col-sm-3 offset-sm-3 mb-1">
      <a href="{{ route( 'admin.temp.ranges.showdates', $Range ) }}" class="form-control btn btn-secondary">Range Dates</a>
    </div>

    <div class="col-sm-4 mb-1">
      <form method="post" action="{{ route( 'admin.temp.ranges.range.toggleactive', $Range ) }}" >
      @csrf
@if ( $Range->is_active )
        <input type="submit" class="form-control btn btn-danger" value="Deactivate Range" />
@else
        <input type="submit" class="form-control btn btn-success" value="Reactivate Range" />
@endif
      </form>
    </div>

  </div>
