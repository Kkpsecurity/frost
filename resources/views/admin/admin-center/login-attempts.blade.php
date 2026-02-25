@extends('adminlte::page')

@section('title', 'Login Attempts')

@section('content_header')
    <h1><i class="fas fa-sign-in-alt"></i> Login Attempts</h1>
@stop

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Not Yet Implemented</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        Login attempt tracking requires a dedicated <code>login_attempts</code> log table.
                        This feature is planned for a future release.
                    </p>
                    <p class="mt-2 mb-0">
                        In the meantime, consider reviewing the
                        <a href="{{ route('admin.admin-center.activity-logs') }}">
                            <i class="fas fa-history"></i> Activity Logs
                        </a>
                        page for active session information.
                    </p>
                </div>
            </div>
        </div>
    </div>

@stop
