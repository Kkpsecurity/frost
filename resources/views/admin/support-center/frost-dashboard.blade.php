@extends('adminlte::page')

@section('title', 'Support Dashboard')

@section('adminlte_css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content_header')
    <!-- Cache bust: {{ now()->timestamp }} - v5 -->
    <div class="row align-items-center mb-4">
        <div class="col text-center">
            <h1 class="h2 mb-3">
                <i class="fas fa-headset text-info mr-3"></i>
                Support Center
            </h1>
            <p class="text-muted mb-0 lead">How can we help you today?</p>
            {{-- Selected Course Indicator --}}
            <div id="header-course-indicator" class="mt-3" style="display: none;">
                <div class="alert alert-info d-inline-block mb-0 py-2 px-4">
                    <i class="fas fa-graduation-cap mr-2"></i>
                    <strong>Selected Course:</strong>
                    <span id="header-course-name">Course Name</span>
                    <button class="btn btn-sm btn-light ml-3" onclick="changeCourse()">
                        <i class="fas fa-exchange-alt"></i> Change
                    </button>
                </div>
            </div>
        </div>
        <div class="col-auto">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="online-status-btn"
                    title="No Student Selected">
                    <i class="fas fa-circle text-secondary" style="font-size: 0.7em;" id="online-status-icon"></i>
                    <span id="online-status-text">Offline</span>
                </button>
                <button type="button" class="btn btn-outline-info btn-sm">
                    <i class="fas fa-clock"></i> {{ now()->format('g:i A') }}
                </button>
                <a href="/admin/frost-support/debug-db" class="btn btn-outline-warning btn-sm" target="_blank">
                    <i class="fas fa-bug"></i> Debug
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            {{-- Main Student Search Section --}}
            <div class="col-12">
                {{-- Student Search Card --}}
                <div class="card">
                    <div class="card-body p-4">
                        {{-- Search Interface --}}
                        @include('admin.support-center.includes.search-interface')

                        {{-- Course Selection --}}
                        @include('admin.support-center.includes.course-selection')

                        {{-- Selected Student Details --}}
                        <div id="student-details" class="mt-4" style="display: none;">
                            <hr>
                            <div class="row">
                                @include('admin.support-center.includes.student-profile-sidebar')
                                @include('admin.support-center.includes.student-details-tabs')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include('admin.support-center.includes.modals')
@endsection

{{-- CSS Styles --}}
@section('css')
    @include('admin.support-center.includes.styles')
@endsection

{{-- JavaScript --}}
@push('js')
    @include('admin.support-center.includes.scripts')
@endpush
