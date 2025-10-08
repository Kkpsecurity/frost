@extends('layouts.app')

@section('title', 'Class Onboarding')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- Onboarding Header -->
            <div class="card shadow-sm border-0 mb-4" 
                 style="background: linear-gradient(135deg, var(--frost-primary-color), var(--frost-secondary-color)); color: white;">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-rocket fa-3x" style="color: var(--frost-highlight-color);"></i>
                    </div>
                    <h2 class="mb-3">Welcome to Your Security Training Class!</h2>
                    <p class="lead mb-4">
                        We've detected that you have a scheduled class session. 
                        Let's get you ready to begin your training.
                    </p>
                    
                    <!-- Course Information -->
                    @if(isset($courseDate))
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="border-end border-light">
                                <h5 class="mb-1">{{ $courseDate->course->name ?? 'Security Training' }}</h5>
                                <small class="opacity-75">Course</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-end border-light">
                                <h5 class="mb-1">{{ $courseDate->starts_at->format('M j, Y') }}</h5>
                                <small class="opacity-75">Date</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h5 class="mb-1">{{ $courseDate->starts_at->format('g:i A') }}</h5>
                            <small class="opacity-75">Time</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Status Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="card-title mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Class Session Detected
                            </h5>
                            <p class="text-muted mb-0">
                                Your student record has been automatically created and you're ready to begin the onboarding process.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-success fs-6 px-3 py-2">
                                <i class="fas fa-user-check me-1"></i>
                                Ready to Start
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Next Steps Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-list-ol me-2"></i>
                        Next Steps
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary rounded-pill">1</span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Complete Attendance</h6>
                                    <p class="text-muted mb-0 small">
                                        Mark your attendance for today's class session
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-secondary rounded-pill">2</span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Begin Onboarding</h6>
                                    <p class="text-muted mb-0 small">
                                        Complete the class onboarding process
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card border-primary shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-clipboard-check fa-2x text-primary"></i>
                            </div>
                            <h6 class="card-title">Mark Attendance</h6>
                            <p class="card-text small text-muted">
                                Record your attendance for today's class
                            </p>
                            <button class="btn btn-primary btn-sm" id="markAttendanceBtn">
                                <i class="fas fa-check me-1"></i>
                                Mark Present
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="card border-info shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-door-open fa-2x text-info"></i>
                            </div>
                            <h6 class="card-title">Begin Onboarding</h6>
                            <p class="card-text small text-muted">
                                Start the class onboarding process
                            </p>
                            <button class="btn btn-info btn-sm" id="beginOnboardingBtn" disabled>
                                <i class="fas fa-arrow-right me-1"></i>
                                Start Process
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Alert -->
            <div class="alert alert-info border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fa-lg me-3"></i>
                    <div>
                        <strong>Please Note:</strong> You must complete attendance marking before proceeding to the onboarding process. 
                        This ensures proper record keeping for your training session.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const markAttendanceBtn = document.getElementById('markAttendanceBtn');
    const beginOnboardingBtn = document.getElementById('beginOnboardingBtn');
    
    // Handle attendance marking
    markAttendanceBtn.addEventListener('click', function() {
        // TODO: Implement attendance marking logic
        console.log('Mark attendance clicked');
        
        // For now, just enable the onboarding button
        this.innerHTML = '<i class="fas fa-check me-1"></i> Attendance Marked';
        this.classList.remove('btn-primary');
        this.classList.add('btn-success');
        this.disabled = true;
        
        // Enable onboarding button
        beginOnboardingBtn.disabled = false;
        beginOnboardingBtn.classList.remove('btn-info');
        beginOnboardingBtn.classList.add('btn-success');
    });
    
    // Handle onboarding start
    beginOnboardingBtn.addEventListener('click', function() {
        // TODO: Implement onboarding navigation
        console.log('Begin onboarding clicked');
        alert('Onboarding process will be implemented in the next phase!');
    });
});
</script>
@endpush
@endsection