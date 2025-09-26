{{-- Frost Messaging Component for AdminLTE Sidebar --}}

{{-- Messaging Toggle Button (Add to navbar) --}}
<li class="nav-item">
    <a class="nav-link" href="#" id="messaging-toggle" title="Messages">
        <i class="fas fa-comments"></i>
        <span class="badge badge-danger" id="messaging-unread-badge" style="display: none;">0</span>
    </a>
</li>

{{-- Messaging Panel (Hidden by default) --}}
<div id="messaging-panel">
    <div class="messaging-panel-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-comments me-2"></i>Messages</h5>
            <button type="button" class="btn btn-sm btn-outline-light" id="new-message-btn">
                <i class="fas fa-plus"></i> New
            </button>
        </div>
    </div>

    <div class="messaging-panel-body">
        {{-- Threads List --}}
        <div id="threads-list-container">
            <div class="p-2 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Recent Conversations</small>
                    <button type="button" class="btn btn-sm btn-link text-muted p-0" onclick="window.frostMessaging.loadThreads()">
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
    <link rel="stylesheet" href="{{ asset('css/messaging.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/messaging.js') }}"></script>
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
