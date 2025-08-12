@extends('layouts.example-with-topbar')

@section('title', 'Topbar Notifications Demo')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">🔔 Topbar Notifications & Messages Demo</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> How to Test the Topbar System</h5>
                    <p>Look at the top-right corner of this page. You should see:</p>
                    <ul>
                        <li><strong>Bell Icon (🔔)</strong> - Click to see notifications dropdown</li>
                        <li><strong>Envelope Icon (📧)</strong> - Click to see messages dropdown</li>
                    </ul>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">📬 Notifications System</h3>
                            </div>
                            <div class="card-body">
                                <p><strong>Features:</strong></p>
                                <ul>
                                    <li>Real-time notification count badge</li>
                                    <li>Dropdown preview with notification details</li>
                                    <li>Mark individual notifications as read</li>
                                    <li>"Mark all as read" functionality</li>
                                    <li>Auto-refresh every 30 seconds</li>
                                </ul>

                                <button class="btn btn-primary" onclick="testNotifications()">
                                    <i class="fas fa-bell"></i> Test Notifications
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title">💬 Messages System</h3>
                            </div>
                            <div class="card-body">
                                <p><strong>Features:</strong></p>
                                <ul>
                                    <li>Unread message count badge</li>
                                    <li>Dropdown preview of recent conversations</li>
                                    <li>Quick access to messaging threads</li>
                                    <li>Full messaging panel for detailed conversations</li>
                                    <li>Real-time message updates</li>
                                </ul>

                                <button class="btn btn-success" onclick="testMessages()">
                                    <i class="fas fa-envelope"></i> Test Messages
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-warning">
                    <div class="card-header">
                        <h3 class="card-title">🧪 Testing Instructions</h3>
                    </div>
                    <div class="card-body">
                        <h5>Step 1: Notifications Bell</h5>
                        <ol>
                            <li>Click the <strong>bell icon (🔔)</strong> in the top-right navbar</li>
                            <li>The dropdown should open showing your notifications</li>
                            <li>Try clicking "Mark all as read"</li>
                            <li>The badge count should update</li>
                        </ol>

                        <h5>Step 2: Messages Envelope</h5>
                        <ol>
                            <li>Click the <strong>envelope icon (📧)</strong> in the top-right navbar</li>
                            <li>The dropdown should show your unread messages</li>
                            <li>Click on a message preview to open the thread</li>
                            <li>The full messaging panel should slide in from the right</li>
                        </ol>

                        <h5>Step 3: Full Messaging Panel</h5>
                        <ol>
                            <li>In the messages dropdown, click "See All Messages"</li>
                            <li>The messaging panel should open with your conversation list</li>
                            <li>Click on any conversation to view messages</li>
                            <li>Try composing and sending a message</li>
                            <li>Use the back arrow or close button to navigate</li>
                        </ol>
                    </div>
                </div>

                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">⚙️ Configuration Options</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Auto-Refresh Settings</h6>
                                <div class="form-group">
                                    <label>Refresh Interval (seconds):</label>
                                    <input type="number" id="refresh-interval" class="form-control" value="30" min="10" max="300">
                                    <button class="btn btn-sm btn-primary mt-2" onclick="updateRefreshInterval()">Update</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Manual Actions</h6>
                                <button class="btn btn-outline-primary btn-sm" onclick="window.frostTopbar.loadNotifications()">
                                    <i class="fas fa-sync"></i> Refresh Notifications
                                </button>
                                <button class="btn btn-outline-success btn-sm" onclick="window.frostTopbar.loadMessages()">
                                    <i class="fas fa-sync"></i> Refresh Messages
                                </button>
                                <button class="btn btn-outline-info btn-sm" onclick="window.frostTopbar.openMessagingPanel()">
                                    <i class="fas fa-comments"></i> Open Messaging Panel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-success">
                    <h6><i class="icon fas fa-check"></i> System Status</h6>
                    <div id="system-status">
                        <p><strong>JavaScript:</strong> <span id="js-status" class="text-muted">Checking...</span></p>
                        <p><strong>API Endpoints:</strong> <span id="api-status" class="text-muted">Checking...</span></p>
                        <p><strong>Authentication:</strong> <span class="text-success">✓ User logged in as {{ auth()->user()->name }}</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Check system status
    setTimeout(checkSystemStatus, 1000);
});

function checkSystemStatus() {
    // Check JavaScript
    if (typeof window.frostTopbar !== 'undefined') {
        $('#js-status').html('<span class="text-success">✓ Topbar system loaded</span>');
    } else {
        $('#js-status').html('<span class="text-danger">✗ Topbar system not loaded</span>');
    }

    // Check API
    fetch('/messaging/threads', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (response.ok) {
            $('#api-status').html('<span class="text-success">✓ API endpoints working</span>');
        } else {
            $('#api-status').html('<span class="text-danger">✗ API error: ' + response.status + '</span>');
        }
    })
    .catch(error => {
        $('#api-status').html('<span class="text-danger">✗ API connection failed</span>');
    });
}

function testNotifications() {
    if (typeof window.frostTopbar !== 'undefined') {
        window.frostTopbar.toggleNotificationsDropdown();
        showAlert('Notifications dropdown should be open. Check the bell icon in the top-right!', 'info');
    } else {
        showAlert('Topbar system not loaded. Please refresh the page.', 'danger');
    }
}

function testMessages() {
    if (typeof window.frostTopbar !== 'undefined') {
        window.frostTopbar.toggleMessagesDropdown();
        showAlert('Messages dropdown should be open. Check the envelope icon in the top-right!', 'info');
    } else {
        showAlert('Topbar system not loaded. Please refresh the page.', 'danger');
    }
}

function updateRefreshInterval() {
    const interval = parseInt($('#refresh-interval').val()) * 1000;
    if (typeof window.frostTopbar !== 'undefined') {
        window.frostTopbar.refreshInterval = interval;
        showAlert(`Refresh interval updated to ${interval/1000} seconds`, 'success');
    } else {
        showAlert('Topbar system not loaded.', 'danger');
    }
}

function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;

    $('#system-status').after(alertHtml);

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        $('.alert-dismissible').alert('close');
    }, 5000);
}
</script>
@endpush
