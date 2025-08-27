@extends('adminlte::page')

@section('title', 'Support Dashboard')

@section('content_header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">
                <i class="fas fa-headset text-info"></i>
                Support Dashboard
            </h1>
            <p class="text-muted mb-0">Quick access to support tools and student assistance</p>
        </div>
        <div class="col-auto">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-success">
                    <i class="fas fa-phone"></i> Available
                </button>
                <button type="button" class="btn btn-outline-info">
                    <i class="fas fa-clock"></i> {{ now()->format('g:i A') }}
                </button>
            </div>
        </div>
    </div>
@endsection

@section('content')
    {{-- Quick Stats --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['open_tickets'] ?? '8' }}</h3>
                    <p>Open Tickets</p>
                </div>
                <div class="icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <a href="#tickets-section" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['resolved_today'] ?? '12' }}</h3>
                    <p>Resolved Today</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="#resolved-section" class="small-box-footer">
                    View History <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['pending_review'] ?? '5' }}</h3>
                    <p>Pending Review</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="#pending-section" class="small-box-footer">
                    Review Now <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['urgent_tickets'] ?? '2' }}</h3>
                    <p>Urgent</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="#urgent-section" class="small-box-footer">
                    View Urgent <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Left Column - Active Tickets --}}
        <div class="col-lg-8">
            {{-- Active Tickets --}}
            <div class="card" id="tickets-section">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-inbox"></i>
                        Active Support Tickets
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> New Ticket
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Student</th>
                                    <th>Subject</th>
                                    <th>Priority</th>
                                    <th>Created</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activeTickets ?? [] as $ticket)
                                    <tr>
                                        <td>
                                            <strong>#{{ $ticket['id'] ?? '001' }}</strong>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $ticket['student_name'] ?? 'John Doe' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $ticket['student_email'] ?? 'john@example.com' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                {{ $ticket['subject'] ?? 'Login Issues' }}
                                                <br>
                                                <small class="text-muted">{{ $ticket['course'] ?? 'Security Fundamentals' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if(($ticket['priority'] ?? 'medium') === 'high')
                                                <span class="badge badge-danger">High</span>
                                            @elseif(($ticket['priority'] ?? 'medium') === 'medium')
                                                <span class="badge badge-warning">Medium</span>
                                            @else
                                                <span class="badge badge-info">Low</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $ticket['created_at'] ?? '2 hours ago' }}</small>
                                        </td>
                                        <td>
                                            @if(($ticket['status'] ?? 'open') === 'open')
                                                <span class="badge badge-primary">Open</span>
                                            @elseif(($ticket['status'] ?? 'open') === 'in_progress')
                                                <span class="badge badge-warning">In Progress</span>
                                            @else
                                                <span class="badge badge-success">Resolved</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-success" 
                                                        title="Reply">
                                                    <i class="fas fa-reply"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        title="Close">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-inbox fa-2x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">No active tickets</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Quick Student Search --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-search"></i>
                        Student Quick Search
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="text" class="form-control" 
                                       placeholder="Search by name, email, or student ID...">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control">
                                <option>All Courses</option>
                                <option>Security Fundamentals</option>
                                <option>Advanced Security</option>
                                <option>Cyber Defense</option>
                            </select>
                        </div>
                    </div>
                    
                    {{-- Search Results --}}
                    <div id="search-results" class="mt-3" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Status</th>
                                        <th>Last Login</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Results will be populated via JavaScript --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Quick Tools and Stats --}}
        <div class="col-lg-4">
            {{-- Quick Actions --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i>
                        Quick Tools
                    </h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-user-plus text-success me-3"></i>
                            <div>
                                <strong>Create Student Account</strong>
                                <br>
                                <small class="text-muted">Add new student to system</small>
                            </div>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-key text-info me-3"></i>
                            <div>
                                <strong>Reset Password</strong>
                                <br>
                                <small class="text-muted">Generate password reset link</small>
                            </div>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-graduation-cap text-warning me-3"></i>
                            <div>
                                <strong>Course Access</strong>
                                <br>
                                <small class="text-muted">Grant or revoke course access</small>
                            </div>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-file-alt text-primary me-3"></i>
                            <div>
                                <strong>Generate Report</strong>
                                <br>
                                <small class="text-muted">Create support activity report</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock"></i>
                        Recent Activity
                    </h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @forelse($recentActivity ?? [] as $activity)
                            <div class="timeline-item">
                                <i class="fas {{ $activity['icon'] ?? 'fa-info-circle' }} bg-{{ $activity['color'] ?? 'info' }}"></i>
                                <div class="timeline-content">
                                    <h6>{{ $activity['title'] ?? 'Activity' }}</h6>
                                    <p class="mb-1">{{ $activity['description'] ?? 'No description available' }}</p>
                                    <small class="text-muted">{{ $activity['time'] ?? '2 hours ago' }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-3">
                                <i class="fas fa-history fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">No recent activity</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Support Metrics --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i>
                        Today's Metrics
                    </h3>
                </div>
                <div class="card-body">
                    <div class="metric-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Avg Response Time</span>
                            <strong class="text-success">{{ $metrics['avg_response'] ?? '15 min' }}</strong>
                        </div>
                        <div class="progress mt-1" style="height: 4px;">
                            <div class="progress-bar bg-success" style="width: 85%"></div>
                        </div>
                    </div>
                    
                    <div class="metric-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Resolution Rate</span>
                            <strong class="text-info">{{ $metrics['resolution_rate'] ?? '92%' }}</strong>
                        </div>
                        <div class="progress mt-1" style="height: 4px;">
                            <div class="progress-bar bg-info" style="width: 92%"></div>
                        </div>
                    </div>
                    
                    <div class="metric-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Customer Satisfaction</span>
                            <strong class="text-warning">{{ $metrics['satisfaction'] ?? '4.8/5' }}</strong>
                        </div>
                        <div class="progress mt-1" style="height: 4px;">
                            <div class="progress-bar bg-warning" style="width: 96%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 15px;
            border-left: 2px solid #e0e0e0;
        }
        .timeline-item:last-child {
            border-left: none;
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
        .timeline-content {
            padding-left: 15px;
        }
        .timeline-content h6 {
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        .timeline-content p {
            margin-bottom: 5px;
            font-size: 0.85rem;
        }
        .metric-item {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .metric-item:last-child {
            border-bottom: none;
        }
        .small-box .inner h3 {
            font-size: 2rem;
        }
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
        }
    </style>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Student search functionality
            $('#search-students').on('click', function() {
                const query = $('#student-search-input').val();
                if (query.length > 2) {
                    // Perform search (replace with actual AJAX call)
                    $('#search-results').show();
                    console.log('Searching for:', query);
                }
            });

            // Auto-refresh ticket counts every 30 seconds
            setInterval(function() {
                // Refresh ticket counts (replace with actual AJAX call)
                console.log('Refreshing ticket counts...');
            }, 30000);

            // Handle ticket actions
            $('.table').on('click', '.btn-outline-primary', function() {
                // View ticket details
                console.log('Viewing ticket details...');
            });

            $('.table').on('click', '.btn-outline-success', function() {
                // Reply to ticket
                console.log('Replying to ticket...');
            });

            $('.table').on('click', '.btn-outline-danger', function() {
                // Close ticket
                if (confirm('Are you sure you want to close this ticket?')) {
                    console.log('Closing ticket...');
                }
            });
        });
    </script>
@endsection
