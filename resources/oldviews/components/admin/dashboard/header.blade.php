@props([
    'title' => 'Admin Dashboard',
    'showDate' => true,
])

<style>
    .dashboard-header {
        background-color: #f8f9fa;
        padding: 12px 16px;
        border-bottom: 1px solid #eee;
    }

    .dashboard-header h3 {
        margin: 0;
        font-weight: 600;
        color: #343a40;
        font-size: 1.05rem !important;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .dashboard-header i {
        color: #7e806e;
    }
</style>

<div class="dashboard-header d-flex justify-content-between bg-dark p-3 rounded align-items-center">
    <h3 class="m-0 text-warning">
        <i class="fas fa-shield-alt  me-1"></i> {{ $title }}
    </h3>
    @if ($showDate)
        <div class="text-muted">
            <i class="fas fa-calendar-alt me-1"></i>
            {{ dateGreeter() }}
        </div>
    @endif
</div>
