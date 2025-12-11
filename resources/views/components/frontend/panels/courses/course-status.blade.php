{{-- Course Status Overview Panel --}}
@php
    // Get dynamic course data from database
    $courses = \App\Services\RCache::Courses()->where('is_active', true);
    $classDCourse = $courses->firstWhere('id', 1); // Class D course
    $classGCourse = $courses->firstWhere('id', 3); // Class G course

    // Calculate available dates and next session
    $today = now()->format('Y-m-d');
    $classDAvailable = 23; // This would come from database query
    $classGAvailable = 6; // This would come from database query
    $nextClassDDate = 'Aug 25'; // Dynamic calculation
    $nextClassGDate = 'Aug 25'; // Dynamic calculation
@endphp

<div class="frost-primary-bg course-status-panel-wrapper py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="text-white">Course Status Overview</h2>
                <h5 class="text-white-50">Current availability and pricing for our security training courses</h5>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="course-status-card d40-card">
                    <div class="card-header">
                        <div class="course-icon d40-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="course-details">
                            <h5 class="course-title">{{ $classDCourse->name ?? 'Class D Security (Armed)' }}</h5>
                            <p class="course-subtitle">{{ $classDCourse->short_description ?? '40-Hour Armed Security Training' }}</p>
                        </div>
                        <div class="course-price">
                            <span class="price">${{ number_format($classDCourse->price ?? 120, 2) }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="stats-row">
                            <div class="stat">
                                <span class="stat-number">{{ $classDAvailable }}</span>
                                <span class="stat-label">Available Dates</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number">{{ $nextClassDDate }}</span>
                                <span class="stat-label">Next Session</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number">40hr</span>
                                <span class="stat-label">Duration</span>
                            </div>
                        </div>
                        <div class="course-actions mt-3">
                            <a href="{{ url('/courses/detail/' . ($classDCourse->id ?? 1)) }}" class="btn btn-outline-light btn-sm me-2">
                                <i class="fas fa-info-circle me-1"></i> Course Details
                            </a>
                            <a href="{{ url('/courses/schedules') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-calendar me-1"></i> View Schedule
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="course-status-card g28-card">
                    <div class="card-header">
                        <div class="course-icon g28-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="course-details">
                            <h5 class="course-title">{{ $classGCourse->name ?? 'Class G Security (Unarmed)' }}</h5>
                            <p class="course-subtitle">{{ $classGCourse->short_description ?? '28-Hour Unarmed Security Training' }}</p>
                        </div>
                        <div class="course-price">
                            <span class="price">${{ number_format($classGCourse->price ?? 65, 2) }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="stats-row">
                            <div class="stat">
                                <span class="stat-number">{{ $classGAvailable }}</span>
                                <span class="stat-label">Available Dates</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number">{{ $nextClassGDate }}</span>
                                <span class="stat-label">Next Session</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number">28hr</span>
                                <span class="stat-label">Duration</span>
                            </div>
                        </div>
                        <div class="course-actions mt-3">
                            <a href="{{ url('/courses/detail/' . ($classGCourse->id ?? 3)) }}" class="btn btn-outline-light btn-sm me-2">
                                <i class="fas fa-info-circle me-1"></i> Course Details
                            </a>
                            <a href="{{ url('/courses/schedules') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-calendar me-1"></i> View Schedule
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Override main-page-content background specifically for this panel */
.main-page-content .frost-primary-bg.course-status-panel-wrapper {
    background: var(--frost-primary-color) !important;
}

.course-status-card {
    background: #f0f4f8;
    border-radius: 16px;
    padding: 0;
    border: 1px solid rgba(255, 255, 255, 0.15);
    transition: all 0.3s ease;
    height: 100%;
    overflow: hidden;
}

.course-status-card:hover {
    background: #e2e8f0;
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
}

.card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 24px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    background: rgba(255, 255, 255, 0.6) !important;
    border-radius: 16px 16px 0 0;
}

.course-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    flex-shrink: 0;
    margin-right: 16px;
}

.d40-icon {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
}

.g28-icon {
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
}

.course-details {
    flex-grow: 1;
}

.course-title {
    color: var(--frost-primary-color);
    font-weight: 600;
    font-size: 1.25rem;
    margin-bottom: 4px;
    line-height: 1.2;
}

.course-subtitle {
    color: rgba(33, 42, 62, 0.7);
    font-size: 0.9rem;
    margin: 0;
}

.course-price {
    text-align: right;
}

.course-price .price {
    color: var(--accent-theme);
    font-size: 1.75rem;
    font-weight: 700;
    display: block;
}

.card-body {
    padding: 24px;
}

.stats-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    padding-top: 15px;
    border-top: 1px solid rgba(33, 42, 62, 0.1);
}

.stat {
    text-align: center;
    flex: 1;
}

.stat-number {
    display: block;
    color: var(--frost-primary-color);
    font-size: 1.4rem;
    font-weight: 700;
    line-height: 1.2;
}

.stat-label {
    display: block;
    color: rgba(33, 42, 62, 0.6);
    font-size: 0.8rem;
    margin-top: 4px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.course-actions {
    display: flex;
    gap: 8px;
}

.course-actions .btn {
    flex: 1;
    font-size: 0.9rem;
    font-weight: 500;
}

.course-actions .btn-outline-light {
    border-color: var(--frost-primary-color);
    color: var(--frost-primary-color);
}

.course-actions .btn-outline-light:hover {
    background-color: var(--frost-primary-color);
    color: white;
}

@media (max-width: 768px) {
    .card-header {
        flex-direction: column;
        text-align: center;
    }

    .course-icon {
        margin: 0 auto 16px auto;
    }

    .course-price {
        text-align: center;
        margin-top: 12px;
    }

    .stats-row {
        flex-direction: column;
        gap: 16px;
    }

    .course-actions {
        flex-direction: column;
    }
}
</style>
