@extends('adminlte::page')

@section('title', 'Notifications')

@section('content_header')
    <h1>Notifications Management</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Notification Settings Overview -->
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bell"></i> System Notifications
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Configure system-wide notification preferences</p>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="emailNotifications" checked>
                            <label class="custom-control-label" for="emailNotifications">Email Notifications</label>
                        </div>
                        <small class="form-text text-muted">Enable email notifications for important events</small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="pushNotifications" checked>
                            <label class="custom-control-label" for="pushNotifications">Push Notifications</label>
                        </div>
                        <small class="form-text text-muted">Enable browser push notifications</small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="smsNotifications">
                            <label class="custom-control-label" for="smsNotifications">SMS Notifications</label>
                        </div>
                        <small class="form-text text-muted">Enable SMS notifications for critical alerts</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog"></i> Notification Types
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Configure which events trigger notifications</p>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="newUserRegistration" checked>
                            <label class="custom-control-label" for="newUserRegistration">New User Registration</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="passwordReset" checked>
                            <label class="custom-control-label" for="passwordReset">Password Reset Requests</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="failedLogin" checked>
                            <label class="custom-control-label" for="failedLogin">Failed Login Attempts</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="systemErrors" checked>
                            <label class="custom-control-label" for="systemErrors">System Errors</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="paymentEvents">
                            <label class="custom-control-label" for="paymentEvents">Payment Events</label>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-info">
                        <i class="fas fa-save"></i> Save Preferences
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Notifications -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Recent Notifications
                    </h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Send Test Notification
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Type</th>
                                <th>Message</th>
                                <th>Recipient</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ now()->subMinutes(5)->format('M d, Y H:i') }}</td>
                                <td><span class="badge badge-primary">Email</span></td>
                                <td>Password reset confirmation</td>
                                <td>user@example.com</td>
                                <td><span class="badge badge-success">Sent</span></td>
                                <td>
                                    <button class="btn btn-xs btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ now()->subHours(1)->format('M d, Y H:i') }}</td>
                                <td><span class="badge badge-warning">Push</span></td>
                                <td>New lesson available</td>
                                <td>All Students</td>
                                <td><span class="badge badge-success">Delivered</span></td>
                                <td>
                                    <button class="btn btn-xs btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ now()->subHours(3)->format('M d, Y H:i') }}</td>
                                <td><span class="badge badge-primary">Email</span></td>
                                <td>Welcome email to new user</td>
                                <td>newuser@example.com</td>
                                <td><span class="badge badge-success">Sent</span></td>
                                <td>
                                    <button class="btn btn-xs btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ now()->subHours(5)->format('M d, Y H:i') }}</td>
                                <td><span class="badge badge-danger">SMS</span></td>
                                <td>Security alert: unusual login</td>
                                <td>+1234567890</td>
                                <td><span class="badge badge-danger">Failed</span></td>
                                <td>
                                    <button class="btn btn-xs btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-xs btn-warning" title="Retry">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    <ul class="pagination pagination-sm m-0 float-right">
                        <li class="page-item"><a class="page-link" href="#">«</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">»</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Statistics -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-envelope"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Email Sent Today</span>
                    <span class="info-box-number">1,247</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-bell"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Push Sent Today</span>
                    <span class="info-box-number">532</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-sms"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">SMS Sent Today</span>
                    <span class="info-box-number">89</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Failed Today</span>
                    <span class="info-box-number">12</span>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .custom-switch {
            padding-left: 2.5rem;
        }
        .info-box {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
    </style>
@stop

@section('js')
    <script>
        console.log('Notifications management page loaded');

        // Add any JavaScript functionality here
        $(document).ready(function() {
            // Handle notification toggle switches
            $('.custom-control-input').on('change', function() {
                const notificationType = $(this).attr('id');
                const isEnabled = $(this).is(':checked');
                console.log(`${notificationType} notifications ${isEnabled ? 'enabled' : 'disabled'}`);

                // You can add an AJAX call here to save the preference
            });
        });
    </script>
@stop
