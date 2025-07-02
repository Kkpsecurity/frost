<div class="mb-4 p-3" style="border: 1px solid #666;">
  {!! nl2br( $Range->range_html ) !!}
</div>

@if ( Auth::user()->IsSysAdmin() )

<h3 class="mt-4">Edit Range HTML</h3>

<form method="post" action="{{ route( 'admin.temp.ranges.updaterangehtml', $Range ) }}">
@csrf
  <textarea class="form-control mb-2" name="range_html" id="range_html" rows="20" required >{{ $Range->range_html }}</textarea>
  <button type="submit" class="form-control btn btn-primary">Update</button>
</form>

@endif
