<?php

$max_records = 10;

#$show_accordion = 'show';
$show_accordion = '';

?>
<div class="accordion" id="add_dates_accordion">

  <div class="card">
    <div class="card-header p-0" id="add_dates_heading">
      <h2 class="mb-0">
        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#add_dates_collapse" aria-expanded="true" aria-controls="add_dates_collapse">
          Add Dates
        </button>
      </h2>
    </div>

	<div id="add_dates_collapse" class="collapse {{ $show_accordion }}" aria-labelledby="add_dates_heading" data-parent="#add_dates_accordion">
      <div class="card-body p-2">

        <form method="post" action="{{ route( 'admin.temp.ranges.adddates', $Range ) }}" autocomplete="off">
        @csrf
        <input type="hidden" name="max_records" value="{{ $max_records }}" />

          <table border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="dates_form_header date_col">Start Date</td>
            <td class="dates_form_header date_col">End Date</td>
            <td class="dates_form_header times_col range_dates_form_header">Times</td>
            <td class="dates_form_header price_col">Price</td>
          </tr>

@foreach ( range( 0, ( $max_records - 1 ) ) as $id )
          <tr>
            <td>
              <input type="text" class="form-control cleave-date" name="start_date_{{ $id }}" id="start_date_{{ $id }}" placeholder="YYYY-MM-DD" />
            </td>
            <td>
              <input type="text" class="form-control cleave-date" name="end_date_{{ $id }}" id="end_date_{{ $id }}" placeholder="YYYY-MM-DD" />
            </td>
            <td>
              <input type="text" class="form-control" name="times_{{ $id }}" id="times_{{ $id }}" placeholder="Times" value="{{ $Range->times }}" />
            </td>
            <td>
              <input type="text" class="form-control cleave-price" name="price_{{ $id }}" id="price_{{ $id }}" placeholder="Price" value="{{ $Range->price }}" />
            </td>
@endforeach

          <tr>
            <td align="center" colspan="2">
              <button type="submit" class="form-control btn btn-primary">Add Range Dates</button>
            </td>
          </tr>

          </table>
        </form>

      </div>
    </div>
  </div>

</div>


<style>

#add_dates_accordion
{
    width:  700px;
    margin: 0 auto;
}

.dates_form_header { padding-left: 18px; }

.date_col  { width: 130px; }
.times_col { width: 350px; }
.price_col { width:  90px; }

</style>
