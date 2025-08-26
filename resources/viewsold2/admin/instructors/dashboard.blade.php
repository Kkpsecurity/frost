{{-- Admin Instructor Dashboard - Uses AdminLTE Package Layout --}}
@extends('adminlte::page')

@section('title', 'Instructor Dashboard')

@section('content_header')
    <x-admin.partials.titlebar
        title="Instructor Dashboard"
        :breadcrumbs="[
            ['title' => 'Instructors', 'url' => route('admin.instructors')],
            ['title' => 'Dashboard']
        ]"
    />
@endsection

@section('content')
    {{-- Quick Stats Cards --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="150" text="Active Students" icon="fas fa-users" theme="info" url="#" url-text="View all students"/>
        </div>
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="12" text="Active Courses" icon="fas fa-book" theme="success" url="#" url-text="Manage courses"/>
        </div>
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="8" text="Live Sessions Today" icon="fas fa-video" theme="warning" url="#" url-text="View schedule"/>
        </div>
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="95%" text="Completion Rate" icon="fas fa-chart-line" theme="danger" url="#" url-text="View reports"/>
        </div>
    </div>

    {{-- React Dashboard Container --}}
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Instructor Portal" theme="lightblue" icon="fas fa-graduation-cap">
                <div id="instructor-dashboard-container">
                    {{-- React components will mount here --}}
                    <div class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Loading instructor dashboard...</p>
                        </div>
                    </div>
                </div>
            </x-adminlte-card>
        </div>
    </div>

    {{-- Additional Admin Cards --}}
    <div class="row">
        <div class="col-md-6">
            <x-adminlte-card title="Recent Activity" theme="primary" icon="fas fa-clock">
                <div class="timeline">
                    <div class="timeline-item">
                        <i class="fas fa-user bg-info"></i>
                        <div class="timeline-content">
                            <h4>New student enrolled</h4>
                            <p>John Doe joined Security Fundamentals course</p>
                            <small class="text-muted">2 hours ago</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <i class="fas fa-video bg-success"></i>
                        <div class="timeline-content">
                            <h4>Live session completed</h4>
                            <p>Advanced Security Training - Session 3</p>
                            <small class="text-muted">4 hours ago</small>
                        </div>
                    </div>
                </div>
            </x-adminlte-card>
        </div>
        <div class="col-md-6">
            <x-adminlte-card title="Quick Actions" theme="secondary" icon="fas fa-tasks">
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-plus text-primary"></i> Create New Course
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-calendar text-success"></i> Schedule Live Session
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-users text-info"></i> Manage Students
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-chart-bar text-warning"></i> View Reports
                    </a>
                </div>
            </x-adminlte-card>
        </div>
    </div>
@endsection

@section('css')
    {{-- Custom timeline styles --}}
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        .timeline-item i {
            position: absolute;
            left: -45px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            color: white;
        }
        .timeline-content h4 {
            margin-bottom: 5px;
            font-size: 1rem;
        }
        .timeline-content p {
            margin-bottom: 5px;
        }
    </style>
@endsection

@section('js')
    {{-- Load the instructor React components --}}
    @vite(['resources/js/instructor.ts'])
@endsection
