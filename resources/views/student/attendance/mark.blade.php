@extends('layouts.app')

@section('title', 'Mark Attendance')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <!-- Attendance Header -->
            <div class="card shadow-sm border-0 mb-4"
                 style="background: linear-gradient(135deg, var(--frost-primary-color), var(--frost-secondary-color)); color: white;">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-clipboard-check fa-3x" style="color: var(--frost-highlight-color);"></i>
                    </div>
                    <h2 class="mb-3">Mark Your Attendance</h2>
                    <p class="lead mb-4">
                        Welcome! Please mark your attendance to enter today's security training class.
                    </p>

                    <!-- Course Information -->
                    @if(isset($courseDate))
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="border-end border-light">
                                <h5 class="mb-1">{{ $course->title ?? 'Security Training' }}</h5>
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

            <!-- Attendance Instructions -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Attendance Instructions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check text-success me-2"></i>Required Steps:</h6>
                            <ul class="list-unstyled ms-3">
                                <li class="mb-2"><i class="fas fa-arrow-right text-primary me-2"></i>Confirm your presence in the classroom</li>
                                <li class="mb-2"><i class="fas fa-arrow-right text-primary me-2"></i>Click "Mark Present" below</li>
                                <li class="mb-2"><i class="fas fa-arrow-right text-primary me-2"></i>Proceed to class onboarding</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-clock text-warning me-2"></i>Important Notes:</h6>
                            <ul class="list-unstyled ms-3">
                                <li class="mb-2"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Must be physically present in classroom</li>
                                <li class="mb-2"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Attendance required before class begins</li>
                                <li class="mb-2"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Cannot be marked remotely</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Marking Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-user-check fa-4x text-success mb-3"></i>
                        <h4>Ready to Mark Attendance?</h4>
                        <p class="text-muted">
                            Click the button below to confirm your attendance and proceed to class onboarding.
                        </p>
                    </div>

                    <button type="button" class="btn btn-success btn-lg px-5 py-3" id="markAttendanceBtn">
                        <i class="fas fa-check-circle me-2"></i>
                        Mark Present
                    </button>

                    <div class="mt-3">
                        <small class="text-muted">
                            By clicking "Mark Present", you confirm that you are physically present in the classroom.
                        </small>
                    </div>

                    <!-- Loading state (hidden by default) -->
                    <div id="loadingState" class="mt-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Marking attendance...</span>
                        </div>
                        <p class="mt-2 text-muted">Marking your attendance...</p>
                    </div>

                    <!-- Success state (hidden by default) -->
                    <div id="successState" class="mt-4" style="display: none;">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5 class="text-success">Attendance Marked Successfully!</h5>
                        <p class="text-muted">Redirecting to class onboarding...</p>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="alert alert-light border">
                <div class="d-flex align-items-center">
                    <i class="fas fa-question-circle fa-lg text-info me-3"></i>
                    <div>
                        <strong>Need Help?</strong> If you're having trouble marking attendance or have questions about the class,
                        please raise your hand or ask your instructor for assistance.
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
    const loadingState = document.getElementById('loadingState');
    const successState = document.getElementById('successState');

    markAttendanceBtn.addEventListener('click', async function() {
        try {
            // Show loading state
            markAttendanceBtn.style.display = 'none';
            loadingState.style.display = 'block';

            const response = await fetch('{{ route("classroom.attendance.mark", $studentUnit->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                // Show success state
                loadingState.style.display = 'none';
                successState.style.display = 'block';

                // Redirect after a short delay
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 2000);

            } else {
                throw new Error(data.message || 'Failed to mark attendance');
            }

        } catch (error) {
            console.error('Error marking attendance:', error);

            // Hide loading state and show error
            loadingState.style.display = 'none';
            markAttendanceBtn.style.display = 'inline-block';

            alert('Error marking attendance: ' + error.message + '. Please try again or ask your instructor for help.');
        }
    });
});
</script>
@endpush
@endsection
