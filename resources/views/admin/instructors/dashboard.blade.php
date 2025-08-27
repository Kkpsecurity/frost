@extends('adminlte::page')

@section('title', $content['page_title'] ?? 'Instructor Dashboard')


@section('content')
    {{-- Render any server-side widgets if present --}}
    @if (isset($content['widgets']) && view()->exists('components.admin.dashboard.user-stats'))
        <x-admin.dashboard.user-stats :widgets="$content['widgets']" />
    @endif

    {{-- React mount point(s) --}}
    <div id="instructor-dashboard-container" style="min-height:200px">
        <div class="d-flex justify-content-center align-items-center" style="min-height:160px;">
            <div class="text-center text-muted">
                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                <div>Loading instructor dashboard...</div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    {{-- Ensure the instructor route JS loads for mounting React (Vite) --}}
    @vite(['resources/js/instructor.ts'])
@endsection
