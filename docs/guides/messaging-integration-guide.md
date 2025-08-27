# Frost Messaging System Integration Guide

## Overview
The Frost Messaging System provides a comprehensive messaging solution integrated with AdminLTE theme. It includes real-time messaging, notifications, and a responsive UI.

## Components Created

### 1. Database & Models
- ✅ Laravel Messenger package installed and configured
- ✅ User model updated with `Messagable` trait
- ✅ Settings helper class for configuration management
- ✅ Migration for `app_settings` table
- ✅ Seeder for default messaging settings

### 2. Routes & API
- ✅ Complete messaging API in `routes/web.php`:
  - `GET /messaging/threads` - List user's threads
  - `GET /messaging/threads/{thread}` - Get thread with messages
  - `POST /messaging/threads` - Create new thread
  - `POST /messaging/threads/{thread}/message` - Send message
  - `POST /messaging/threads/{thread}/read` - Mark thread as read
  - `GET /messaging/notifications` - Get messaging notifications
  - `POST /messaging/notifications/{id}/read` - Mark notification as read

### 3. Frontend Components
- ✅ `public/js/messaging.js` - JavaScript messaging functionality
- ✅ `public/css/messaging.css` - Messaging styles for AdminLTE
- ✅ `resources/views/components/messaging.blade.php` - Blade component

### 4. Notifications
- ✅ `app/Notifications/NewMessageNotification.php` - Database notifications

## Integration Steps

### Step 1: Include in AdminLTE Layout
Add the messaging component to your main AdminLTE layout file:

```blade
{{-- In your main layout file (e.g., layouts/app.blade.php) --}}
<head>
    {{-- Other head content --}}
    @stack('styles')
</head>

<body>
    {{-- AdminLTE navbar --}}
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav ml-auto">
            {{-- Include messaging toggle button --}}
            @include('components.messaging')
        </ul>
    </nav>

    {{-- Rest of AdminLTE layout --}}
    
    {{-- Before closing body tag --}}
    @stack('scripts')
</body>
```

### Step 2: Ensure CSRF Token
Make sure your layout includes the CSRF token meta tag:

```blade
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Other meta tags --}}
</head>
```

### Step 3: Include jQuery and AdminLTE
Ensure jQuery is loaded before the messaging script:

```blade
<script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
@stack('scripts')
```

## Configuration

### Messaging Settings
Configure messaging behavior by updating the settings:

```php
use App\Support\Settings;

// Update messaging settings
Settings::put('messaging', [
    'allow_new_threads_roles' => ['admin', 'instructor', 'support'],
    'max_thread_participants' => 10,
    'enable_notifications' => true,
    'notification_sound' => true,
    'auto_refresh_interval' => 30000, // milliseconds
]);
```

### User Roles
Ensure your User model has a `role` attribute or modify the routes to use your role system.

## Features

### 1. Real-time Messaging
- Thread-based conversations
- Real-time message updates
- Unread message badges
- Auto-refresh every 30 seconds

### 2. User Interface
- AdminLTE-integrated design
- Responsive messaging panel
- Thread list with unread indicators
- Message composition and sending

### 3. Notifications
- Database notifications for new messages
- Unread message count in navbar
- Notification management endpoints

### 4. Security
- User authentication required
- Thread participation validation
- Role-based thread creation permissions

## Usage Examples

### JavaScript API
```javascript
// Access the messaging instance
const messaging = window.frostMessaging;

// Manually refresh threads
messaging.loadThreads();

// Open a specific thread
messaging.openThread(threadId);

// Toggle the messaging panel
messaging.toggleMessagingPanel();
```

### PHP API Usage
```php
use Cmgmyr\Messenger\Models\Thread;
use Cmgmyr\Messenger\Models\Message;

// Get user's threads
$threads = Thread::forUser(auth()->id())->latest()->get();

// Get unread count
$unreadCount = auth()->user()->newThreadsCount();

// Send a message
$message = Message::create([
    'thread_id' => $threadId,
    'user_id' => auth()->id(),
    'body' => 'Hello world!'
]);
```

## Customization

### Styling
Modify `public/css/messaging.css` to match your theme:

```css
/* Custom color scheme */
#messaging-panel {
    border-color: your-color;
}

.messaging-panel-header {
    background: your-color;
}
```

### JavaScript Behavior
Modify `public/js/messaging.js` to add custom functionality:

```javascript
// Custom event handlers
$(document).on('message-sent', function(event, data) {
    // Custom logic when message is sent
});

// Modify auto-refresh interval
setInterval(() => messaging.loadThreads(), 60000); // 1 minute
```

## Testing

### Manual Testing
1. ✅ Navigate to `/admin/dashboard` (or any authenticated page)
2. ✅ Click the messaging icon in the navbar
3. ✅ Panel should slide in from the right
4. ✅ Threads should load automatically
5. ✅ Create test messages through API or database

### API Testing
```bash
# Test thread listing
curl -H "Authorization: Bearer {token}" http://frost.test/messaging/threads

# Test message sending
curl -X POST -H "Authorization: Bearer {token}" \
     -H "Content-Type: application/json" \
     -d '{"body":"Test message"}' \
     http://frost.test/messaging/threads/1/message
```

## Next Steps

### Optional Enhancements
1. **Real-time Updates**: Implement WebSocket/Pusher for instant messaging
2. **File Attachments**: Add file upload support to messages
3. **Message Search**: Add search functionality across conversations
4. **Message Status**: Add read receipts and delivery status
5. **Mobile App**: Create mobile API endpoints
6. **Emoji Support**: Add emoji picker to message composer

### Performance Optimization
1. **Pagination**: Add pagination to thread and message loading
2. **Caching**: Implement Redis caching for frequent queries
3. **Database Indexing**: Add proper indexes for message queries
4. **Queue Processing**: Move notification sending to background jobs

## Troubleshooting

### Common Issues
1. **CSRF Token Missing**: Ensure meta tag is present in layout
2. **jQuery Not Loaded**: Include jQuery before messaging.js
3. **Permissions Error**: Check user roles in messaging settings
4. **Styles Not Applied**: Verify CSS file is included and paths are correct

### Debug Mode
Enable debug logging in messaging.js:

```javascript
// Add to messaging.js constructor
this.debug = true;

// Debug logging
if (this.debug) console.log('Threads loaded:', this.threads);
```
