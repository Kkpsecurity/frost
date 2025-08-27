@extends('adminlte::page')

@section('title', $pageTitle ?? 'Frost Support Center')

@section('content')
    {{-- Server-side widgets or alerts could go here --}}
    <div id="support-dashboard-container" style="min-height:240px">
        <div class="d-flex justify-content-center align-items-center" style="min-height:180px;">
            <div class="text-center text-muted">
                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                <div>Loading Frost Support dashboard...</div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    {{-- Load the compiled support entry so the React app mounts --}}
    @vite(['resources/js/support.ts'])
@endsection
