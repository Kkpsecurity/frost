<form method="post" action="{{ route( 'admin.temp.ranges.store' ) }}" autocomplete="off" >
@csrf

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="name">Range Name</label>
    <div class="col">
      <input type="text" class="form-control" name="name" id="name" value="{{ old( 'name' ) }}" required />
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="city">City</label>
    <div class="col">
      <input type="text" class="form-control" name="city" id="city" value="{{ old( 'city' ) }}" required />
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="address">Address</label>
    <div class="col">
      <textarea class="form-control" name="address" id="address" rows="2" required >{{ old( 'address' ) }}</textarea>
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="inst_name">Instructor</label>
    <div class="col">
      <input type="text" class="form-control" name="inst_name" id="inst_name" value="{{ old( 'inst_name' ) }}" required />
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="inst_email">Email</label>
    <div class="col">
      <input type="email" class="form-control" name="inst_email" id="inst_email" value="{{ old( 'inst_email' ) }}" />
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="inst_phone">Phone</label>
    <div class="col-sm-3">
      <input type="text" class="form-control" name="inst_phone" id="inst_phone" value="{{ old( 'inst_phone' ) }}" placeholder="(xxx) xxx-xxxx" />
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="price">Price</label>
    <div class="col-sm-3">
      <input type="text" class="form-control cleave-price" name="price" id="price" value="{{ old( 'price' ) }}" />
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-3 col-form-label" for="times">Times</label>
    <div class="col">
      <input type="text" class="form-control" name="times" id="times" value="{{ old( 'times' ) }}" />
    </div>
  </div>

  {{-- this is awful --}}
  <div class="row mb-3">
    <div class="col offset-sm-3">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="appt_only" id="appt_only" />
        <label class="form-check-label" for="appt_only">By Appointment Only</label>
      </div>
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm col-form-label" for="times">Range Info</label>
    <textarea class="form-control mb-2" name="range_html" id="range_html" rows="20" required >{{ old( 'range_html' ) }}</textarea>
  </div>

  <div class="row mb-3">
    <div class="col-4">
      <input type="submit" class="form-control btn btn-primary" value="Create Range" />
    </div>
  </div>

</form>
