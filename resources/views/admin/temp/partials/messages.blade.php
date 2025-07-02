<?php

$alert_types = [
    'success', 'error', 'danger', 'warning', 'info',
    'primary', 'secondary', 'light', 'dark'
];

$messages = [];

foreach ( $alert_types as $alert_type )
{
    if ( $message = Session::get( $alert_type ) )
    {

        if ( $alert_type == 'error' )
        {
            $alert_type = 'danger';
        }

        $messages[ $alert_type ] = $message;

    }
}

?>
{{--  Bootstrap v4  --}}
@if ( $messages )
<div class="px-3 py-2">
@foreach ( $messages as $alert_type => $message )
  <div class="alert alert-{{ $alert_type }} alert-dismissible fade show" role="alert">
    {!! $message !!}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <i class="fa fa-times" aria-hidden="true"></i>
    </button>
  </div>
@endforeach
</div>
@endif
