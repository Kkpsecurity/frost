{{-- Frost Topbar Notifications & Messages Component --}}

{{-- Notifications Dropdown --}}
<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#" id="notifications-toggle" title="Notifications">
        <i class="far fa-bell"></i>
        <span class="badge badge-warning navbar-badge" id="notifications-count" style="display: none;">0</span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="notifications-dropdown">
        <span class="dropdown-item dropdown-header" id="notifications-header">0 Notifications</span>
        <div class="dropdown-divider"></div>

        {{-- Notifications List --}}
        <div id="notifications-list" style="max-height: 300px; overflow-y: auto;">
            <div class="text-center p-3">
                <div class="loading-spinner"></div>
                <div class="small text-muted mt-2">Loading notifications...</div>
            </div>
        </div>

        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item dropdown-footer" id="mark-all-notifications-read">Mark all as read</a>
    </div>
</li>

{{-- Messages Dropdown --}}
<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#" id="messages-toggle" title="Messages">
        <i class="far fa-envelope"></i>
        <span class="badge badge-danger navbar-badge" id="messages-count" style="display: none;">0</span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="messages-dropdown">
        <span class="dropdown-item dropdown-header" id="messages-header">0 Messages</span>
        <div class="dropdown-divider"></div>

        {{-- Messages List --}}
        <div id="messages-list" style="max-height: 300px; overflow-y: auto;">
            <div class="text-center p-3">
                <div class="loading-spinner"></div>
                <div class="small text-muted mt-2">Loading messages...</div>
            </div>
        </div>

        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item dropdown-footer" id="view-all-messages">
            <i class="fas fa-envelope mr-2"></i>See All Messages
        </a>
    </div>
</li>

{{-- Full Messages Panel (Hidden by default) --}}
<div id="messaging-panel" style="display: none;">
    <div class="messaging-panel-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-comments me-2"></i>Messages</h5>
            <div>
                <button type="button" class="btn btn-sm btn-outline-light" id="new-message-btn">
                    <i class="fas fa-plus"></i> New
                </button>
                <button type="button" class="btn btn-sm btn-outline-light" id="close-messaging-panel">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="messaging-panel-body">
        {{-- Threads List --}}
        <div id="threads-list-container">
            <div class="p-2 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Recent Conversations</small>
                    <button type="button" class="btn btn-sm btn-link text-muted p-0" onclick="window.frostTopbar.loadThreads()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div id="threads-list">
                <div class="text-center p-3">
                    <div class="loading-spinner"></div>
                    <div class="small text-muted mt-2">Loading messages...</div>
                </div>
            </div>
        </div>

        {{-- Thread View (Hidden by default) --}}
        <div id="thread-view" style="display: none;">
            {{-- Content will be populated by JavaScript --}}
        </div>
    </div>
</div>

{{-- Required CSS and JS --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/topbar-notifications.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/topbar-notifications.js') }}"></script>
@endpush

{{-- Initialize CSRF Token for AJAX requests --}}
@if(!isset($__csrf_token_set))
    @push('scripts')
        <script>
            // Ensure CSRF token is available for AJAX requests
            if (!$('meta[name="csrf-token"]').length) {
                $('head').append('<meta name="csrf-token" content="{{ csrf_token() }}">');
            }
        </script>
    @endpush
    @php $__csrf_token_set = true; @endphp
@endif
