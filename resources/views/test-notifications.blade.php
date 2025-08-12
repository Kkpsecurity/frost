@extends('adminlte::page')

@section('title', 'Test Notifications')

@section('content_header')
    <h1>Test AdminLTE Notifications</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Notification System Test</h3>
                </div>
                <div class="card-body">
                    <p>This page tests the AdminLTE notification bell and message envelope in the navbar.</p>

                    <h5>Features:</h5>
                    <ul>
                        <li>🔔 <strong>Notification Bell:</strong> Click to open right sidebar with notifications</li>
                        <li>📧 <strong>Message Envelope:</strong> Click to open right sidebar with messaging system</li>
                        <li>🔄 <strong>Auto-refresh:</strong> Notifications update every 30 seconds</li>
                        <li>🎯 <strong>Right Sidebar:</strong> AdminLTE control sidebar shows content</li>
                        <li>🎨 <strong>Card Layout:</strong> Clean card-based notification/message display</li>
                        <li>✨ <strong>Smooth Transitions:</strong> Sidebar closes/reloads/reopens when switching between notifications and messages</li>
                    </ul>

                    <h5>Right Sidebar Integration:</h5>
                    <ul>
                        <li>AdminLTE right sidebar enabled for notifications and messages</li>
                        <li>Click notification bell to view notifications in right sidebar</li>
                        <li>Click message envelope to view messaging system in right sidebar</li>
                        <li>Custom <code>adminlte-right-sidebar-notifications.js</code> handles the functionality</li>
                        <li>Integrates with existing messaging API endpoints</li>
                    </ul>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Check the navbar above!</strong> Click the notification bell 🔔 or message envelope 📧 to open the right sidebar.
                        The right sidebar will show notifications or messages respectively.
                    </div>

                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <strong>Configuration Status:</strong> AdminLTE right sidebar is enabled and notification widgets are configured.
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-arrow-right"></i>
                        <strong>Try it now:</strong> Click the bell or envelope icons in the navbar to see the right sidebar in action!
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        console.log('Test page loaded');

        // Check if our notification system is loaded
        if (typeof window.frostNotifications !== 'undefined') {
            console.log('✅ Frost AdminLTE Right Sidebar Notifications system is loaded');
        } else {
            console.log('❌ Frost AdminLTE Right Sidebar Notifications system is NOT loaded');
        }

        // Check for AdminLTE navbar notification items
        const bellIcon = $('#notifications-toggle').length;
        const envelopeIcon = $('#messages-toggle').length;
        const rightSidebar = $('.control-sidebar').length;

        console.log('🔔 Bell icon found:', bellIcon > 0);
        console.log('📧 Envelope icon found:', envelopeIcon > 0);
        console.log('📱 Right sidebar found:', rightSidebar > 0);

        // Add click handlers to test the sidebar
        if (bellIcon > 0) {
            $('#notifications-toggle').on('click', function() {
                console.log('🔔 Notification bell clicked - should open right sidebar');
            });
        }

        if (envelopeIcon > 0) {
            $('#messages-toggle').on('click', function() {
                console.log('📧 Message envelope clicked - should open right sidebar');
            });
        }        // Test API endpoints
        setTimeout(() => {
            console.log('Testing API endpoints...');

            fetch('/messaging/notifications', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('✅ Notifications API status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('📊 Notifications data:', data);
            })
            .catch(error => {
                console.log('❌ Notifications API error:', error);
            });

            fetch('/messaging/threads', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('✅ Messages API status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('📊 Messages data:', data);
            })
            .catch(error => {
                console.log('❌ Messages API error:', error);
            });
        }, 1000);
    });
</script>
@stop
