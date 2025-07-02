@extends('layouts.admin')


@section('content')
@include( 'admin.temp.partials.messages' )

  <div class="widewrapper">

    <h3>Site Configs</h3>

    <div class="container">

@foreach ( $SiteConfigs as $SiteConfig )
      <form method="post" action="{{ route( 'admin.temp.site_configs.update', $SiteConfig ) }}" autocomplete="off">
      @csrf
      <div class="row align-items-start mb-2">

        <label class="col-lg-4 col-md-5 col-sm-6 col-form-label">
          {{ $SiteConfig->config_name }}
        </label>

        <div class="col">
@switch( $SiteConfig->cast_to )
@case( 'bool' )
          <input type="checkbox" name="admin_config_value" {{ $SiteConfig->config_value ? 'checked' : '' }}>
@break
@case( 'int' )
          <input type="text" class="form-control cleave-int" name="admin_config_value" value="{{ $SiteConfig->config_value }}" required />
@break
@case( 'float' )
          <input type="text" class="form-control cleave-float" name="admin_config_value" value="{{ $SiteConfig->config_value }}" required />
@break
@case( 'text' )
          <input type="text" class="form-control" name="admin_config_value" value="{{ $SiteConfig->config_value }}" required />
@break
@case( 'longtext' )
          <textarea class="form-control" name="admin_config_value" rows="3" required>{{ $SiteConfig->config_value }}</textarea>
@break
@case( 'htmltext' )
          <textarea class="form-control" name="admin_config_value" rows="3" required>{{ $SiteConfig->config_value }}</textarea>
@break
@default
  <div class="mono unknown_cast_to">Unknown cast_to '{{ $SiteConfig->cast_to }}'</div>
@break
@endswitch
        </div>

        <div class="col-sm-1">
          <button type="submit" class="btn btn-primary">Update</button>
        </div>

      </div>
      </form>

@endforeach

    </div>

  </div>


@include( 'admin.temp.partials.asset-loader' )

<style>

input[type='text']
{
    width: 100%;
}

input[type='checkbox']
{
    margin: 12px 0 0 4px;
    transform: scale(1.4);
}

.unknown_cast_to
{
    margin-top: 12px;
    color:      red;
}

</style>

@endsection
