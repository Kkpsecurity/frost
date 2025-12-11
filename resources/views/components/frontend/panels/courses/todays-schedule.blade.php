@if (isset($course['schedule']))
    <div class="course-schedule-section mb-5">
        <h3 class="section-title text-white">Upcoming Sessions</h3>
        <div class="schedule-content">
            @foreach ($course['schedule'] as $session)
                <div class="schedule-item p-3 rounded mb-3"
                    style="border: 1px solid rgba(255, 255, 255, 0.2); background: rgba(255, 255, 255, 0.05);">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="schedule-date text-white-50">
                                <i class="fas fa-calendar text-info me-2"></i>
                                {{ $session['date'] ?? 'TBD' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="schedule-time text-white-50">
                                <i class="fas fa-clock text-info me-2"></i>
                                {{ $session['time'] ?? '9:00 AM - 5:00 PM' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="schedule-location text-white-50">
                                <i class="fas fa-map-marker-alt text-info me-2"></i>
                                {{ $session['location'] ?? 'Online/Hybrid' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="schedule-availability text-end">
                                <span class="badge {{ $session['available'] ?? true ? 'bg-success' : 'bg-danger' }}">
                                    {{ $session['available'] ?? true ? 'Available' : 'Full' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="course-schedule-section mb-5">
        <h3 class="section-title text-white">Upcoming Sessions</h3>
        <div class="alert alert-info text-white" role="alert">
            <i class="fas fa-info-circle me-2 text-white"></i>No scheduled sessions available at this time. Please check back later or contact us for more information.
        </div>
    </div>
@endif
