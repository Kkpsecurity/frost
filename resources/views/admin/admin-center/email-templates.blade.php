@extends('adminlte::page')

@section('title', 'Email Templates')

@section('content_header')
    <h1>Email Templates</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Email Templates Overview -->
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-envelope"></i> Welcome Email
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Sent when a new user registers</p>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7"><span class="badge badge-success">Active</span></dd>
                        
                        <dt class="col-sm-5">Subject:</dt>
                        <dd class="col-sm-7">Welcome to Frost!</dd>
                        
                        <dt class="col-sm-5">Last Updated:</dt>
                        <dd class="col-sm-7">{{ now()->subDays(5)->format('M d, Y') }}</dd>
                    </dl>
                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editTemplateModal" onclick="editTemplate('welcome')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-info" onclick="previewTemplate('welcome')">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                    <button class="btn btn-sm btn-success" onclick="testEmail('welcome')">
                        <i class="fas fa-paper-plane"></i> Test Send
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-key"></i> Password Reset
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Sent when user requests password reset</p>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7"><span class="badge badge-success">Active</span></dd>
                        
                        <dt class="col-sm-5">Subject:</dt>
                        <dd class="col-sm-7">Reset Your Password</dd>
                        
                        <dt class="col-sm-5">Last Updated:</dt>
                        <dd class="col-sm-7">{{ now()->subDays(10)->format('M d, Y') }}</dd>
                    </dl>
                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editTemplateModal" onclick="editTemplate('password_reset')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-info" onclick="previewTemplate('password_reset')">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                    <button class="btn btn-sm btn-success" onclick="testEmail('password_reset')">
                        <i class="fas fa-paper-plane"></i> Test Send
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-check-circle"></i> Email Verification
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Sent to verify user's email address</p>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7"><span class="badge badge-success">Active</span></dd>
                        
                        <dt class="col-sm-5">Subject:</dt>
                        <dd class="col-sm-7">Verify Your Email</dd>
                        
                        <dt class="col-sm-5">Last Updated:</dt>
                        <dd class="col-sm-7">{{ now()->subDays(15)->format('M d, Y') }}</dd>
                    </dl>
                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editTemplateModal" onclick="editTemplate('email_verification')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-info" onclick="previewTemplate('email_verification')">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                    <button class="btn btn-sm btn-success" onclick="testEmail('email_verification')">
                        <i class="fas fa-paper-plane"></i> Test Send
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shopping-cart"></i> Order Confirmation
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Sent when an order is completed</p>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7"><span class="badge badge-success">Active</span></dd>
                        
                        <dt class="col-sm-5">Subject:</dt>
                        <dd class="col-sm-7">Order Confirmation</dd>
                        
                        <dt class="col-sm-5">Last Updated:</dt>
                        <dd class="col-sm-7">{{ now()->subDays(3)->format('M d, Y') }}</dd>
                    </dl>
                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editTemplateModal" onclick="editTemplate('order_confirmation')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-info" onclick="previewTemplate('order_confirmation')">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                    <button class="btn btn-sm btn-success" onclick="testEmail('order_confirmation')">
                        <i class="fas fa-paper-plane"></i> Test Send
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-graduation-cap"></i> Course Enrollment
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Sent when user enrolls in a course</p>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7"><span class="badge badge-success">Active</span></dd>
                        
                        <dt class="col-sm-5">Subject:</dt>
                        <dd class="col-sm-7">Course Enrollment</dd>
                        
                        <dt class="col-sm-5">Last Updated:</dt>
                        <dd class="col-sm-7">{{ now()->subDays(7)->format('M d, Y') }}</dd>
                    </dl>
                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editTemplateModal" onclick="editTemplate('course_enrollment')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-info" onclick="previewTemplate('course_enrollment')">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                    <button class="btn btn-sm btn-success" onclick="testEmail('course_enrollment')">
                        <i class="fas fa-paper-plane"></i> Test Send
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bell"></i> Notification Email
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">General notification template</p>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7"><span class="badge badge-success">Active</span></dd>
                        
                        <dt class="col-sm-5">Subject:</dt>
                        <dd class="col-sm-7">Notification</dd>
                        
                        <dt class="col-sm-5">Last Updated:</dt>
                        <dd class="col-sm-7">{{ now()->subDays(12)->format('M d, Y') }}</dd>
                    </dl>
                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editTemplateModal" onclick="editTemplate('notification')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-info" onclick="previewTemplate('notification')">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                    <button class="btn btn-sm btn-success" onclick="testEmail('notification')">
                        <i class="fas fa-paper-plane"></i> Test Send
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Variables -->
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-code"></i> Available Template Variables
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>User Variables</h5>
                    <ul class="list-unstyled">
                        <li><code>@{{ $user->fullname() }}</code> - User's full name</li>
                        <li><code>@{{ $user->fname }}</code> - User's first name</li>
                        <li><code>@{{ $user->lname }}</code> - User's last name</li>
                        <li><code>@{{ $user->email }}</code> - User's email address</li>
                    </ul>

                    <h5>System Variables</h5>
                    <ul class="list-unstyled">
                        <li><code>@{{ $app_name }}</code> - Application name</li>
                        <li><code>@{{ $app_url }}</code> - Application URL</li>
                        <li><code>@{{ $support_email }}</code> - Support email</li>
                        <li><code>@{{ $current_year }}</code> - Current year</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Order Variables</h5>
                    <ul class="list-unstyled">
                        <li><code>@{{ $order->id }}</code> - Order ID</li>
                        <li><code>@{{ $order->total_price }}</code> - Order total</li>
                        <li><code>@{{ $order->completed_at }}</code> - Completion date</li>
                    </ul>

                    <h5>Course Variables</h5>
                    <ul class="list-unstyled">
                        <li><code>@{{ $course->name }}</code> - Course name</li>
                        <li><code>@{{ $course->description }}</code> - Course description</li>
                    </ul>

                    <h5>Action Variables</h5>
                    <ul class="list-unstyled">
                        <li><code>@{{ $action_url }}</code> - Action button URL</li>
                        <li><code>@{{ $action_text }}</code> - Action button text</li>
                        <li><code>@{{ $reset_url }}</code> - Password reset URL</li>
                        <li><code>@{{ $verify_url }}</code> - Email verification URL</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- SMTP Settings -->
    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-server"></i> SMTP Configuration
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Mail Driver:</dt>
                        <dd class="col-sm-8">{{ config('mail.default', 'smtp') }}</dd>

                        <dt class="col-sm-4">SMTP Host:</dt>
                        <dd class="col-sm-8">{{ config('mail.mailers.smtp.host', 'Not configured') }}</dd>

                        <dt class="col-sm-4">SMTP Port:</dt>
                        <dd class="col-sm-8">{{ config('mail.mailers.smtp.port', 'Not configured') }}</dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">From Address:</dt>
                        <dd class="col-sm-8">{{ config('mail.from.address', 'Not configured') }}</dd>

                        <dt class="col-sm-4">From Name:</dt>
                        <dd class="col-sm-8">{{ config('mail.from.name', 'Not configured') }}</dd>

                        <dt class="col-sm-4">Encryption:</dt>
                        <dd class="col-sm-8">{{ config('mail.mailers.smtp.encryption', 'none') }}</dd>
                    </dl>
                </div>
            </div>
            <div class="alert alert-warning mt-3">
                <i class="fas fa-info-circle"></i>
                SMTP settings are configured in your <code>.env</code> file. 
                Update <code>MAIL_*</code> variables to change email configuration.
            </div>
        </div>
    </div>

    <!-- Edit Template Modal -->
    <div class="modal fade" id="editTemplateModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Email Template</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="#" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" class="form-control" id="template_subject" name="subject" placeholder="Email subject">
                        </div>

                        <div class="form-group">
                            <label>Email Content (HTML)</label>
                            <textarea class="form-control" id="template_content" name="content" rows="15" placeholder="HTML email content"></textarea>
                            <small class="form-text text-muted">
                                Use the variables listed above. HTML and CSS are supported.
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="template_active" name="is_active" checked>
                                <label class="custom-control-label" for="template_active">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    function editTemplate(templateName) {
        // TODO: Load template data via AJAX
        console.log('Editing template:', templateName);
        
        // Example data (would be loaded from database)
        const templates = {
            'welcome': {
                subject: 'Welcome to Frost!',
                content: '<h1>Welcome @{{$user->fname}}!</h1><p>Thank you for joining Frost...</p>'
            },
            'password_reset': {
                subject: 'Reset Your Password',
                content: '<h1>Password Reset</h1><p>Click the link to reset: @{{$reset_url}}</p>'
            }
        };
        
        if (templates[templateName]) {
            $('#template_subject').val(templates[templateName].subject);
            $('#template_content').val(templates[templateName].content);
        }
    }

    function previewTemplate(templateName) {
        alert('Preview functionality for ' + templateName + ' template.\nThis would open a new window with the rendered email.');
    }

    function testEmail(templateName) {
        const email = prompt('Enter email address to send test email to:', '{{ auth()->user()->email }}');
        if (email) {
            alert('Test email for ' + templateName + ' would be sent to: ' + email + '\nImplementation pending.');
            // TODO: Implement AJAX call to send test email
        }
    }
</script>
@stop
