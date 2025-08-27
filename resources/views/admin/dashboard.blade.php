
@extends('adminlte::page')

@section('title', $content['page_title'] ?? 'Admin Dashboard')

@section('content_header')
    @if(isset($content['breadcrumbs']))
        <x-admin.partials.titlebar :title="$content['page_title'] ?? 'Admin'" :breadcrumbs="$content['breadcrumbs']" />
    @else
        <x-admin.partials.titlebar title="Admin" :breadcrumbs="[['title' => 'Admin', 'url' => url('admin')], ['title' => 'Dashboard']]" />
    @endif
@endsection

@section('content')
    {{-- Load dashboard widgets/components. These components live under resources/views/components/admin --}}
    <div class="row">
        <div class="col-12">
            @if(isset($content['widgets']))
                {{-- Example: user stats card component --}}
                @if(view()->exists('components.admin.dashboard.user-stats'))
                    <x-admin.dashboard.user-stats :widgets="$content['widgets']" />
                @endif

                {{-- Fallback simple panel listing available widgets --}}
                <div class="card mt-3">
                    <div class="card-body">
                        <h4>Available Widgets</h4>
                        <ul>
                            @foreach($content['widgets']['available_widgets'] ?? [] as $w)
                                <li>{{ $w }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <h4>Welcome to the Admin Dashboard</h4>
                        <p>No widgets configured. Please check the dashboard service configuration.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- React mount points for instructor/support apps --}}
    @include('admin.partials.react-mounts')

@endsection

@section('css')
    {{-- custom admin css can be added here --}}
@endsection

@section('js')
    {{-- custom admin js can be added here --}}
@endsection
