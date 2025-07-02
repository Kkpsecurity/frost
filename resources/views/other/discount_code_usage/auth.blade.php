@extends( 'other.discount_code_usage.layout' )

@section( 'content' )

  <div class="card d-table mx-auto my-4">
    <div class="card-header">
      Authentication Required
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route( 'discount_codes.usage.auth', $DiscountCode ) }}" autocomplete="off">
      @csrf
        <div class="row px-3 mb-3">
         Enter the matching Discount Code
        </div>
        <div class="row">
          <div class="col-md-auto mb-2">
            <input type="text" class="form-control @error( 'discount_code' ) is-invalid @enderror" name="discount_code" id="discount_code" value="{{ request()->input( 'discount_code' ) }}" />
@error( 'discount_code' )
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
@enderror
          </div>
          <div class="col-md-auto mb-2">
            <input type="submit" class="form-control btn btn-primary" value="Submit" />
          </div>
        </div>
      </form>
    </div>
  </div>

@endsection
