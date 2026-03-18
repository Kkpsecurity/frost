@extends('adminlte::page')

@section('title', 'Student Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Student Details</h1>
        <div>
            <a href="{{ route('admin.students.export-account', $student->id) }}" class="btn btn-success mr-2">
                <i class="fas fa-file-csv"></i> Export Audit CSV
            </a>
            <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Students
            </a>
        </div>
    </div>
@stop

@section('content')

    {{-- Stats Row --}}
    <div class="row">
        <div class="col-6 col-sm-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-graduation-cap"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Courses</span>
                    <span class="info-box-number">{{ $stats['total_courses'] }}</span>
                    <span class="info-box-text text-muted">{{ $stats['active_courses'] }} active</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Completed</span>
                    <span class="info-box-number">{{ $stats['completed_courses'] }}</span>
                    <span class="info-box-text text-muted">courses passed</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-book-open"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Units</span>
                    <span class="info-box-number">{{ $stats['completed_units'] }}/{{ $stats['total_units'] }}</span>
                    <span class="info-box-text text-muted">completed</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Paid</span>
                    <span class="info-box-number">${{ number_format($stats['total_spent'], 2) }}</span>
                    @if ($stats['pending_payments'] > 0)
                        <span class="info-box-text text-warning">{{ $stats['pending_payments'] }} pending</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Left Column: Profile --}}
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle" src="{{ $student->getAvatar('regular') }}"
                            alt="Student profile picture">
                    </div>
                    <h3 class="profile-username text-center">
                        {{ $student->fname }}
                        @if (!empty($student->student_info['initials']))
                            {{ $student->student_info['initials'] }}
                        @endif
                        {{ $student->lname }}
                        @if (!empty($student->student_info['suffix']))
                            {{ $student->student_info['suffix'] }}
                        @endif
                    </h3>
                    <p class="text-muted text-center">User ID: <strong>#{{ $student->id }}</strong></p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Email</b>
                            <span class="float-right">{{ $student->email }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Email Verified</b>
                            <span class="float-right">
                                @if ($student->email_verified_at)
                                    <span class="badge badge-success">Verified</span>
                                    <small
                                        class="text-muted d-block">{{ $student->email_verified_at->format('M d, Y') }}</small>
                                @else
                                    <span class="badge badge-warning">Unverified</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item">
                            <b>Status</b>
                            <span class="float-right">
                                @if ($student->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </span>
                        </li>
                        @if (!empty($student->student_info['phone']))
                            <li class="list-group-item">
                                <b>Phone</b>
                                <span class="float-right">{{ $student->student_info['phone'] }}</span>
                            </li>
                        @endif
                        @if (!empty($student->student_info['dob']))
                            <li class="list-group-item">
                                <b>Date of Birth</b>
                                <span class="float-right">{{ $student->student_info['dob'] }}</span>
                            </li>
                        @endif
                        @if (!empty($student->student_info['address']))
                            <li class="list-group-item">
                                <b>Address</b>
                                <span class="float-right text-right" style="max-width:60%">
                                    {{ $student->student_info['address'] }}<br>
                                    @if (!empty($student->student_info['address2']))
                                        {{ $student->student_info['address2'] }}<br>
                                    @endif
                                    {{ $student->student_info['city'] ?? '' }}
                                    @if (!empty($student->student_info['state']))
                                        , {{ $student->student_info['state'] }}
                                    @endif
                                    {{ $student->student_info['zip'] ?? '' }}
                                </span>
                            </li>
                        @endif
                        <li class="list-group-item">
                            <b>Registered</b>
                            <span class="float-right">{{ $student->created_at->format('M d, Y') }}</span>
                        </li>
                    </ul>

                    <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-primary btn-block">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                </div>
            </div>

            @if (!empty($student->student_info['notes']))
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-sticky-note"></i> Admin Notes</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-sm">{{ $student->student_info['notes'] }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Column: Courses, Payments, Activity --}}
        <div class="col-md-8">

            {{-- Enrolled Courses --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-graduation-cap"></i> Enrolled Courses</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Course</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Enrolled</th>
                                    <th>Started</th>
                                    <th>Completed</th>
                                    <th>Expires</th>
                                    <th>DOL #</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($student->courseAuths as $ca)
                                    @php
                                        if ($ca->disabled_at) {
                                            $caStatus = ['label' => 'Disabled', 'class' => 'badge-danger'];
                                        } elseif ($ca->completed_at && $ca->is_passed) {
                                            $caStatus = ['label' => 'Passed', 'class' => 'badge-success'];
                                        } elseif ($ca->completed_at && !$ca->is_passed) {
                                            $caStatus = ['label' => 'DNC', 'class' => 'badge-warning'];
                                        } elseif ($ca->start_date) {
                                            $caStatus = ['label' => 'In Progress', 'class' => 'badge-info'];
                                        } elseif ($ca->agreed_at) {
                                            $caStatus = ['label' => 'Not Started', 'class' => 'badge-primary'];
                                        } else {
                                            $caStatus = ['label' => 'Pending', 'class' => 'badge-secondary'];
                                        }
                                        $isExpired =
                                            $ca->expire_date && \Carbon\Carbon::parse($ca->expire_date)->isPast();
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $ca->course->title ?? 'Course #' . $ca->course_id }}</strong>
                                            @if ($ca->id_override)
                                                <span class="badge badge-dark ml-1"
                                                    title="Admin override active">Override</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($ca->course?->course_type && $ca->course->course_type !== 'standard')
                                                <span
                                                    class="badge badge-info">{{ strtoupper($ca->course->course_type) }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $caStatus['class'] }}">{{ $caStatus['label'] }}</span>
                                            @if ($isExpired)
                                                <span class="badge badge-danger ml-1">Expired</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">{{ $ca->created_at->format('M d, Y') }}</td>
                                        <td class="text-nowrap">
                                            {{ $ca->start_date ? $ca->start_date->format('M d, Y') : '—' }}</td>
                                        <td class="text-nowrap">
                                            {{ $ca->completed_at ? $ca->completed_at->format('M d, Y') : '—' }}</td>
                                        <td class="text-nowrap">
                                            @if ($ca->expire_date)
                                                <span class="{{ $isExpired ? 'text-danger font-weight-bold' : '' }}">
                                                    {{ \Carbon\Carbon::parse($ca->expire_date)->format('M d, Y') }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($ca->dol_tracking)
                                                <code>{{ $ca->dol_tracking }}</code>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if ($ca->disabled_at)
                                        <tr class="table-danger">
                                            <td colspan="8" class="text-sm text-danger py-1 pl-4">
                                                <i class="fas fa-ban"></i>
                                                Disabled {{ $ca->disabled_at->format('M d, Y') }}:
                                                {{ $ca->disabled_reason ?? 'No reason provided' }}
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-3">No courses enrolled</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Payment History --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-credit-card"></i> Payment History</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Order #</th>
                                <th>Course</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td><code>#{{ $order->id }}</code></td>
                                    <td>{{ $order->course->title ?? 'Course #' . $order->course_id }}</td>
                                    <td>${{ number_format($order->total_price, 2) }}</td>
                                    <td>
                                        @if ($order->refunded_at)
                                            <span class="badge badge-warning">Refunded</span>
                                        @elseif($order->completed_at)
                                            <span class="badge badge-success">Completed</span>
                                        @else
                                            <span class="badge badge-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">{{ $order->created_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No payment records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Enrolled Units --}}
            @if ($studentUnits->isNotEmpty())
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-book"></i> Enrolled Units</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Unit</th>
                                    <th>Course Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($studentUnits as $studentUnit)
                                    <tr>
                                        <td>
                                            <strong>{{ $studentUnit->courseUnit->title ?? 'Unit #' . $studentUnit->course_unit_id }}</strong>
                                        </td>
                                        <td>
                                            @if ($studentUnit->courseDate)
                                                {{ $studentUnit->courseDate->date->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($studentUnit->unit_completed)
                                                <span class="badge badge-success">Completed</span>
                                            @else
                                                <span class="badge badge-warning">In Progress</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Activity Timeline --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-history"></i> Account Activity</h3>
                </div>
                <div class="card-body">
                    @php
                        $timeline = collect();

                        // Account creation
                        $timeline->push([
                            'date' => $student->created_at,
                            'icon' => 'fa-user-plus',
                            'color' => 'bg-success',
                            'title' => 'Account Created',
                            'body' => 'Registered on ' . $student->created_at->format('F j, Y g:i A'),
                        ]);

                        if ($student->email_verified_at) {
                            $timeline->push([
                                'date' => $student->email_verified_at,
                                'icon' => 'fa-envelope-open',
                                'color' => 'bg-info',
                                'title' => 'Email Verified',
                                'body' => 'Verified on ' . $student->email_verified_at->format('F j, Y'),
                            ]);
                        }

                        foreach ($student->courseAuths as $ca) {
                            $courseName = $ca->course->title ?? 'Course #' . $ca->course_id;
                            $timeline->push([
                                'date' => $ca->created_at,
                                'icon' => 'fa-graduation-cap',
                                'color' => 'bg-primary',
                                'title' => 'Enrolled: ' . $courseName,
                                'body' => 'Course enrollment on ' . $ca->created_at->format('F j, Y'),
                            ]);

                            if ($ca->start_date) {
                                $timeline->push([
                                    'date' => $ca->start_date->startOfDay(),
                                    'icon' => 'fa-play-circle',
                                    'color' => 'bg-info',
                                    'title' => 'Started: ' . $courseName,
                                    'body' => 'Classroom started on ' . $ca->start_date->format('F j, Y'),
                                ]);
                            }

                            if ($ca->completed_at) {
                                $icon = $ca->is_passed ? 'fa-trophy' : 'fa-times-circle';
                                $color = $ca->is_passed ? 'bg-success' : 'bg-warning';
                                $label = $ca->is_passed ? 'Passed' : 'DNC';
                                $timeline->push([
                                    'date' => $ca->completed_at,
                                    'icon' => $icon,
                                    'color' => $color,
                                    'title' => $label . ': ' . $courseName,
                                    'body' => 'Course marked ' . $label . ' on ' . $ca->completed_at->format('F j, Y'),
                                ]);
                            }

                            if ($ca->disabled_at) {
                                $timeline->push([
                                    'date' => $ca->disabled_at,
                                    'icon' => 'fa-ban',
                                    'color' => 'bg-danger',
                                    'title' => 'Disabled: ' . $courseName,
                                    'body' => $ca->disabled_reason ?? 'No reason given',
                                ]);
                            }
                        }

                        foreach ($orders as $order) {
                            if ($order->completed_at) {
                                $timeline->push([
                                    'date' => $order->completed_at,
                                    'icon' => 'fa-credit-card',
                                    'color' => 'bg-teal',
                                    'title' => 'Payment: $' . number_format($order->total_price, 2),
                                    'body' =>
                                        ($order->course->title ?? 'Course #' . $order->course_id) .
                                        ' — Order #' .
                                        $order->id,
                                ]);
                            }
                        }

                        $timeline = $timeline->sortByDesc('date')->values();
                    @endphp

                    <div class="timeline timeline-inverse">
                        @foreach ($timeline as $event)
                            <div>
                                <i class="fas {{ $event['icon'] }} {{ $event['color'] }}"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock"></i> {{ $event['date']->diffForHumans() }}
                                    </span>
                                    <h3 class="timeline-header">{{ $event['title'] }}</h3>
                                    <div class="timeline-body text-sm text-muted">{{ $event['body'] }}</div>
                                </div>
                            </div>
                        @endforeach
                        <div><i class="fas fa-clock bg-gray"></i></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop

@section('css')
    <style>
        .profile-user-img {
            border: 3px solid #adb5bd;
            margin: 0 auto;
            padding: 3px;
            width: 100px;
        }
    </style>
@stop
