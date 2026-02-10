{{-- Orders/Courses Section --}}
<div class="orders-section">
    <h3 class="text-white mb-4">
        <i class="fas fa-graduation-cap me-2"></i>My Courses
    </h3>

    {{-- Summary Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-dark border-secondary">
                <div class="card-body text-center">
                    <h4 class="text-primary mb-1">{{ $data['total_courses'] }}</h4>
                    <p class="text-white-50 mb-0">Total Courses</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark border-secondary">
                <div class="card-body text-center">
                    <h4 class="text-success mb-1">{{ $data['active_courses'] }}</h4>
                    <p class="text-white-50 mb-0">Active Courses</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark border-secondary">
                <div class="card-body text-center">
                    <h4 class="text-info mb-1">{{ $data['completed_courses'] }}</h4>
                    <p class="text-white-50 mb-0">Completed</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Course List --}}
    @if($data['course_enrollments']->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Status</th>
                        <th>Enrolled Date</th>
                        <th>Completion</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['course_enrollments'] as $enrollment)
                        <tr>
                            <td>
                                <div>
                                    <strong class="text-white">{{ $enrollment['course_name'] }}</strong>
                                    <br>
                                    <small class="text-white-50">{{ $enrollment['course_code'] }}</small>
                                </div>
                            </td>
                            <td>
                                @if($enrollment['status'] === 'Active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-white-50">{{ $enrollment['enrolled_date'] }}</td>
                            <td>
                                @if($enrollment['completion_status'] === 'Completed')
                                    <span class="badge bg-info">Completed</span>
                                @else
                                    <span class="badge bg-warning">In Progress</span>
                                @endif
                            </td>
                            <td>
                                <a href="/classroom" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-play me-1"></i>View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            You are not enrolled in any courses yet. <a href="/courses" class="alert-link">Browse available courses</a>
        </div>
    @endif
</div>
