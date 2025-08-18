<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-calendar-alt"></i> Course Dates Schedule
        </h3>
        <div class="card-tools">
            <span class="badge badge-secondary">{{ $content['course_dates']->total() }} total</span>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        @if($content['course_dates']->count() > 0)
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Unit</th>
                        <th>Start Date/Time</th>
                        <th>End Date/Time</th>
                        <th>Duration</th>
                        <th>Instructor</th>
                        <th>Students</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($content['course_dates'] as $courseDate)
                        <tr>
                            <td>
                                <strong>{{ $courseDate->CourseUnit->Course->title }}</strong>
                                @if($courseDate->CourseUnit->Course->needs_range)
                                    <span class="badge badge-warning badge-sm ml-1">Range</span>
                                @endif
                            </td>
                            <td>{{ $courseDate->CourseUnit->title }}</td>
                            <td>
                                <div>{{ \Carbon\Carbon::parse($courseDate->starts_at)->format('M j, Y') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($courseDate->starts_at)->format('g:i A') }}</small>
                            </td>
                            <td>
                                <div>{{ \Carbon\Carbon::parse($courseDate->ends_at)->format('M j, Y') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($courseDate->ends_at)->format('g:i A') }}</small>
                            </td>
                            <td>
                                @php
                                    $duration = \Carbon\Carbon::parse($courseDate->starts_at)->diffInMinutes(\Carbon\Carbon::parse($courseDate->ends_at));
                                    $hours = intval($duration / 60);
                                    $minutes = $duration % 60;
                                @endphp
                                {{ $hours }}h {{ $minutes }}m
                            </td>
                            <td>
                                @if($courseDate->InstUnit && $courseDate->InstUnit->User)
                                    <div>{{ $courseDate->InstUnit->User->fname }} {{ $courseDate->InstUnit->User->lname }}</div>
                                    <small class="text-muted">{{ $courseDate->InstUnit->User->email }}</small>
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $courseDate->StudentUnits->count() }}</span>
                            </td>
                            <td>
                                @if($courseDate->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif

                                @php
                                    $now = now();
                                    $start = \Carbon\Carbon::parse($courseDate->starts_at);
                                    $end = \Carbon\Carbon::parse($courseDate->ends_at);
                                @endphp

                                @if($now < $start)
                                    <span class="badge badge-warning badge-sm ml-1">Upcoming</span>
                                @elseif($now >= $start && $now <= $end)
                                    <span class="badge badge-primary badge-sm ml-1">In Progress</span>
                                @else
                                    <span class="badge badge-secondary badge-sm ml-1">Past</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.course-dates.show', $courseDate) }}"
                                       class="btn btn-info btn-sm" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.course-dates.edit', $courseDate) }}"
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-{{ $courseDate->is_active ? 'secondary' : 'success' }} btn-sm toggle-active-btn"
                                            data-id="{{ $courseDate->id }}"
                                            data-active="{{ $courseDate->is_active ? 1 : 0 }}"
                                            title="{{ $courseDate->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas fa-{{ $courseDate->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                    @if($courseDate->StudentUnits->count() == 0)
                                        <button type="button"
                                                class="btn btn-danger btn-sm delete-btn"
                                                data-id="{{ $courseDate->id }}"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-4">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No course dates found</h5>
                <p class="text-muted">Try adjusting your filters or create a new course date.</p>
                <a href="{{ route('admin.course-dates.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Course Date
                </a>
            </div>
        @endif
    </div>
    @if($content['course_dates']->hasPages())
        <div class="card-footer">
            {{ $content['course_dates']->appends(request()->query())->links() }}
        </div>
    @endif
</div>
