@extends('adminlte::page')

@section('title', 'Student: ' . $student->name)

@section('content_header')
    <x-admin.partials.titlebar
        title="Student Details"
        :breadcrumbs="[
            ['title' => 'Admin', 'url' => url('admin')],
            ['title' => 'Students', 'url' => route('admin.students.dashboard')],
            ['title' => $student->name]
        ]"
    />
@endsection

@section('content')
    <div class="row">
        {{-- Student Information Card --}}
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle"
                             src="{{ $student->profile_photo_url ?? '/images/default-avatar.png' }}"
                             alt="Student profile picture">
                    </div>

                    <h3 class="profile-username text-center">{{ $student->name }}</h3>

                    <p class="text-muted text-center">
                        @if($student->is_active)
                            <span class="badge badge-success">Active Student</span>
                        @else
                            <span class="badge badge-secondary">Inactive Student</span>
                        @endif
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Email</b> <span class="float-right">{{ $student->email }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Phone</b> <span class="float-right">{{ $student->phone ?? 'Not provided' }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Member Since</b> <span class="float-right">{{ $student->created_at->format('M d, Y') }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Last Login</b> <span class="float-right">{{ $student->last_login_at ? $student->last_login_at->format('M d, Y') : 'Never' }}</span>
                        </li>
                    </ul>

                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('admin.students.manage.edit', $student) }}" class="btn btn-primary btn-block">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                        <div class="col-6">
                            @if($student->is_active)
                                <form method="POST" action="{{ route('admin.students.manage.deactivate', $student) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-block" onclick="return confirm('Deactivate this student?')">
                                        <i class="fas fa-times"></i> Deactivate
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.students.manage.activate', $student) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Activate this student?')">
                                        <i class="fas fa-check"></i> Activate
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Stats Card --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Stats</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-shopping-cart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Orders</span>
                                    <span class="info-box-number">{{ $orders->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-book"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Active Courses</span>
                                    <span class="info-box-number">{{ $student->activeCourseAuths->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-graduation-cap"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Completed Courses</span>
                                    <span class="info-box-number">{{ $student->inactiveCourseAuths->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Student Activity and Details --}}
        <div class="col-md-8">
            {{-- Navigation Tabs --}}
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#orders" data-toggle="tab">Orders</a></li>
                        <li class="nav-item"><a class="nav-link" href="#courses" data-toggle="tab">Courses</a></li>
                        <li class="nav-item"><a class="nav-link" href="#payments" data-toggle="tab">Payments</a></li>
                        <li class="nav-item"><a class="nav-link" href="#activity" data-toggle="tab">Activity</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        {{-- Orders Tab --}}
                        <div class="active tab-pane" id="orders">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Course</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orders as $order)
                                            <tr>
                                                <td>#{{ $order->id }}</td>
                                                <td>{{ $order->Course->course_name ?? 'N/A' }}</td>
                                                <td>${{ number_format($order->course_price ?? 0, 2) }}</td>
                                                <td>
                                                    @if($order->is_completed)
                                                        <span class="badge badge-success">Completed</span>
                                                    @else
                                                        <span class="badge badge-warning">Pending</span>
                                                    @endif
                                                </td>
                                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No orders found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Courses Tab --}}
                        <div class="tab-pane" id="courses">
                            <h5>Active Courses</h5>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Course</th>
                                            <th>Progress</th>
                                            <th>Start Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($student->activeCourseAuths as $courseAuth)
                                            <tr>
                                                <td>{{ $courseAuth->course->course_name ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="progress progress-xs">
                                                        <div class="progress-bar bg-success" style="width: {{ $courseAuth->progress ?? 0 }}%"></div>
                                                    </div>
                                                    <span class="badge badge-secondary">{{ $courseAuth->progress ?? 0 }}%</span>
                                                </td>
                                                <td>{{ $courseAuth->created_at->format('M d, Y') }}</td>
                                                <td><span class="badge badge-success">Active</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No active courses</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <h5 class="mt-4">Completed Courses</h5>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Course</th>
                                            <th>Completion Date</th>
                                            <th>Grade</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($student->inactiveCourseAuths as $courseAuth)
                                            <tr>
                                                <td>{{ $courseAuth->course->course_name ?? 'N/A' }}</td>
                                                <td>{{ $courseAuth->completed_at ? $courseAuth->completed_at->format('M d, Y') : 'N/A' }}</td>
                                                <td>{{ $courseAuth->grade ?? 'N/A' }}</td>
                                                <td><span class="badge badge-secondary">Completed</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No completed courses</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Payments Tab --}}
                        <div class="tab-pane" id="payments">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Payment ID</th>
                                            <th>Order</th>
                                            <th>Amount</th>
                                            <th>Method</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $payments = $orders->flatMap(function($order) {
                                                return $order->payments ?? collect();
                                            });
                                        @endphp
                                        @forelse($payments as $payment)
                                            <tr>
                                                <td>#{{ $payment->id }}</td>
                                                <td>#{{ $payment->order_id }}</td>
                                                <td>${{ number_format($payment->amount, 2) }}</td>
                                                <td>{{ ucfirst($payment->method ?? 'N/A') }}</td>
                                                <td>
                                                    @if($payment->status === 'completed')
                                                        <span class="badge badge-success">Completed</span>
                                                    @elseif($payment->status === 'pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @else
                                                        <span class="badge badge-danger">{{ ucfirst($payment->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No payments found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Activity Tab --}}
                        <div class="tab-pane" id="activity">
                            <div class="timeline">
                                <div class="time-label">
                                    <span class="bg-red">Recent Activity</span>
                                </div>

                                {{-- Account Creation --}}
                                <div>
                                    <i class="fas fa-user bg-blue"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i> {{ $student->created_at->diffForHumans() }}</span>
                                        <h3 class="timeline-header">Account Created</h3>
                                        <div class="timeline-body">
                                            Student account was created and activated.
                                        </div>
                                    </div>
                                </div>

                                {{-- Recent Orders --}}
                                @foreach($orders->take(5) as $order)
                                    <div>
                                        <i class="fas fa-shopping-cart bg-green"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i> {{ $order->created_at->diffForHumans() }}</span>
                                            <h3 class="timeline-header">New Order</h3>
                                            <div class="timeline-body">
                                                Ordered course: {{ $order->Course->course_name ?? 'N/A' }}
                                                <br>Amount: ${{ number_format($order->course_price ?? 0, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div>
                                    <i class="fas fa-clock bg-gray"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
