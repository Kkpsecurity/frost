
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

    @if(isset($content['widgets']))
        {{-- User Stats Component --}}
        @if(view()->exists('components.admin.dashboard.user-stats'))
            <x-admin.dashboard.user-stats :widgets="$content['widgets']" />
        @endif
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>Welcome to the Admin Dashboard</h4>
                        <p>No widgets configured. Please check the dashboard service configuration.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- React mount points for instructor/support apps --}}
    @include('admin.partials.react-mounts')

@endsection

@section('css')
    {{-- custom admin css can be added here --}}
@endsection

@section('js')
    {{-- custom admin js can be added here --}}
    <script>
        $(document).ready(function() {
            console.log('🏠 DASHBOARD DEBUG:');
            console.log('- Body classes on load:', $('body').attr('class'));
            console.log('- Control sidebar elements:', $('.control-sidebar').length);
            console.log('- Control sidebar visible:', $('.control-sidebar').is(':visible'));
            console.log('- Control sidebar classes:', $('.control-sidebar').attr('class'));

            // Check if sidebar is auto-opened
            setTimeout(function() {
                console.log('🏠 DASHBOARD AFTER 2s:');
                console.log('- Body classes:', $('body').attr('class'));
                console.log('- Sidebar visible:', $('.control-sidebar').is(':visible'));
            }, 2000);
        });
    </script>
@endsection
