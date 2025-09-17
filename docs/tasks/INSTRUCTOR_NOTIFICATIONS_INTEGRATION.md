# Instructor Dashboard Notification System Integration

## Overview
Design and implement a comprehensive notification system for the instructor dashboard that informs students, support, and instructors about school activities and status updates.

## Notification System Architecture

### Core Components

#### 1. Database Schema
```sql
-- Notifications table
CREATE TABLE notifications (
    id BIGSERIAL PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data JSONB DEFAULT '{}',
    recipient_type VARCHAR(20) NOT NULL, -- 'user', 'role', 'all'
    recipient_id BIGINT NULL,
    sender_id BIGINT NULL,
    channels JSONB DEFAULT '["dashboard"]', -- ['dashboard', 'email', 'sms']
    priority VARCHAR(10) DEFAULT 'normal', -- 'low', 'normal', 'high', 'urgent'
    read_at TIMESTAMP NULL,
    sent_at TIMESTAMP NULL,
    scheduled_for TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- Notification settings for user preferences  
CREATE TABLE notification_settings (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL,
    user_type VARCHAR(20) NOT NULL, -- 'admin', 'user'
    notification_type VARCHAR(50) NOT NULL,
    channels JSONB DEFAULT '["dashboard"]',
    enabled BOOLEAN DEFAULT true,
    quiet_hours_start TIME NULL,
    quiet_hours_end TIME NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(user_id, user_type, notification_type)
);
```

#### 2. Notification Types & Recipients

```php
// Notification type configuration
'notification_types' => [
    'course_schedule' => [
        'name' => 'Course Schedule Changes',
        'recipients' => ['students', 'instructors', 'support'],
        'default_channels' => ['dashboard', 'email'],
        'triggers' => [
            'class_created' => 'New class scheduled',
            'class_updated' => 'Class schedule changed', 
            'class_cancelled' => 'Class cancelled',
            'class_starting' => 'Class starting in 15 minutes',
            'class_started' => 'Class has started',
            'class_ended' => 'Class has ended'
        ]
    ],
    'student_activity' => [
        'name' => 'Student Activity Updates',
        'recipients' => ['instructors'],
        'default_channels' => ['dashboard'],
        'triggers' => [
            'student_joined' => 'Student joined class',
            'student_left' => 'Student left class',
            'assignment_submitted' => 'Assignment submitted',
            'exam_completed' => 'Exam completed',
            'attendance_marked' => 'Attendance updated'
        ]
    ],
    'instructor_activity' => [
        'name' => 'Instructor Activity',
        'recipients' => ['support', 'students'],
        'default_channels' => ['dashboard'],
        'triggers' => [
            'instructor_assigned' => 'Instructor assigned to class',
            'instructor_changed' => 'Class instructor changed',
            'lesson_started' => 'Lesson started by instructor',
            'lesson_completed' => 'Lesson marked complete'
        ]
    ],
    'system_status' => [
        'name' => 'System & Technical Updates',
        'recipients' => ['support', 'instructors'],
        'default_channels' => ['dashboard', 'email'],
        'triggers' => [
            'system_maintenance' => 'Scheduled maintenance',
            'technical_issue' => 'Technical issue reported',
            'zoom_connection' => 'Zoom connection status',
            'backup_completed' => 'System backup completed'
        ]
    ],
    'announcements' => [
        'name' => 'General Announcements',
        'recipients' => ['all'],
        'default_channels' => ['dashboard', 'email'],
        'triggers' => [
            'school_announcement' => 'School-wide announcement',
            'policy_update' => 'Policy or procedure update',
            'holiday_schedule' => 'Holiday schedule changes'
        ]
    ]
]
```

### 3. Dashboard Integration Points

#### Header Notification Bell
- **Location**: AdminLTE header navigation
- **Features**: Unread count badge, dropdown with recent notifications
- **Real-time**: WebSocket updates or polling every 30 seconds

#### Today's Lessons Section  
- **Inline Notifications**: Course-specific alerts within lesson cards
- **Status Indicators**: Visual indicators for course status changes
- **Action Buttons**: Quick actions triggered by notifications

#### Class Overview Stats
- **Alert Badges**: Performance alerts and enrollment notifications
- **Trend Indicators**: Statistical change notifications

#### Real-time Activity Feed
- **Live Updates**: Student activity, instructor actions, system events
- **Filtering**: By notification type, priority, or time range
- **Actions**: Mark as read, dismiss, respond to notifications

## Implementation Plan

### Phase 1: Database Models & Migrations

#### Files to Create:
```php
// database/migrations/2024_01_01_000001_create_notifications_table.php
// database/migrations/2024_01_01_000002_create_notification_settings_table.php
// app/Models/Notification.php
// app/Models/NotificationSetting.php
```

#### Model Relationships:
```php
// Notification.php
class Notification extends Model {
    public function recipient() {
        return $this->morphTo();
    }
    
    public function sender() {
        return $this->morphTo();
    }
}

// User.php - Add notification relationships
public function notifications() {
    return $this->morphMany(Notification::class, 'recipient');
}

public function notificationSettings() {
    return $this->hasMany(NotificationSetting::class);
}
```

### Phase 2: Notification Service Implementation

#### Files to Create:  
```php
// app/Services/Notifications/NotificationService.php
// app/Services/Notifications/NotificationChannelService.php
// app/Services/Notifications/NotificationPreferenceService.php
```

#### Core Service Methods:
```php
class NotificationService {
    public function send(string $type, array $recipients, array $data): bool
    public function sendToUser(User $user, string $type, array $data): bool
    public function sendToRole(string $role, string $type, array $data): bool
    public function markAsRead(int $notificationId, int $userId): bool
    public function getUnreadCount(int $userId): int
    public function getRecentNotifications(int $userId, int $limit = 10): Collection
}
```

### Phase 3: Event-Driven Notification Triggers

#### Files to Create:
```php  
// app/Events/CourseScheduleChanged.php
// app/Events/StudentJoinedClass.php
// app/Events/InstructorAssigned.php
// app/Events/SystemMaintenance.php

// app/Listeners/SendCourseNotification.php
// app/Listeners/SendActivityNotification.php
// app/Listeners/SendSystemNotification.php
```

#### Event Integration:
```php
// CourseDate model - dispatch events on changes
protected $dispatchesEvents = [
    'created' => CourseScheduleChanged::class,
    'updated' => CourseScheduleChanged::class,
    'deleted' => CourseScheduleChanged::class,
];

// InstUnit model - instructor activity events
protected $dispatchesEvents = [
    'created' => InstructorAssigned::class,
    'updated' => InstructorActivityChanged::class,
];
```

### Phase 4: Dashboard Controller Integration

#### Files to Update:
```php
// app/Http/Controllers/Admin/Instructors/InstructorDashboardController.php
```

#### New Controller Methods:
```php
public function getUnreadNotifications()
{
    $admin = auth('admin')->user();
    $notifications = $this->notificationService->getRecentNotifications($admin->id);
    $unreadCount = $this->notificationService->getUnreadCount($admin->id);
    
    return response()->json([
        'notifications' => $notifications,
        'unread_count' => $unreadCount
    ]);
}

public function markNotificationAsRead(Request $request)
{
    $notificationId = $request->input('notification_id');
    $admin = auth('admin')->user();
    
    $success = $this->notificationService->markAsRead($notificationId, $admin->id);
    
    return response()->json(['success' => $success]);
}

public function updateNotificationSettings(Request $request)
{
    $admin = auth('admin')->user();
    $settings = $request->input('settings');
    
    $updated = $this->notificationService->updateUserSettings($admin->id, $settings);
    
    return response()->json(['success' => $updated]);
}
```

### Phase 5: Frontend Notification Components

#### Files to Create/Update:
```tsx
// resources/js/React/Instructor/components/NotificationBell.tsx
// resources/js/React/Instructor/components/NotificationDropdown.tsx  
// resources/js/React/Instructor/components/NotificationItem.tsx
// resources/js/React/Instructor/components/NotificationSettings.tsx
```

#### Component Integration:
```tsx
// NotificationBell.tsx - Header component
const NotificationBell: React.FC = () => {
    const { data: notifications, refetch } = useQuery({
        queryKey: ['instructor-notifications'],
        queryFn: () => instructorApi.getNotifications(),
        refetchInterval: 30000 // Poll every 30 seconds
    });

    return (
        <div className="nav-item dropdown">
            <a className="nav-link" data-toggle="dropdown" href="#">
                <i className="far fa-bell"></i>
                {notifications?.unread_count > 0 && (
                    <span className="badge badge-warning navbar-badge">
                        {notifications.unread_count}
                    </span>
                )}
            </a>
            <NotificationDropdown notifications={notifications?.notifications || []} />
        </div>
    );
};
```

### Phase 6: Real-time Updates Implementation

#### WebSocket Integration (Optional):
```php
// app/Events/NotificationSent.php - Broadcast event
class NotificationSent implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public function broadcastOn(): Channel {
        return new PrivateChannel('instructor.' . $this->recipientId);
    }
}
```

#### Polling Alternative:
```tsx  
// React hook for notification polling
const useNotifications = (userId: number) => {
    return useQuery({
        queryKey: ['notifications', userId],
        queryFn: () => instructorApi.getNotifications(),
        refetchInterval: 30000,
        onSuccess: (data) => {
            // Update notification count in header
            // Show toast for new notifications
        }
    });
};
```

## Dashboard Integration Scenarios

### 1. Course Schedule Notifications

#### Trigger: New class scheduled
- **Recipients**: Enrolled students, assigned instructor, support team
- **Dashboard Display**: 
  - New course card appears in upcoming section
  - Notification bell shows new count
  - Toast notification appears
- **Content**: "New Florida D40 class scheduled for Sept 25, 2024 at 8:00 AM"

#### Trigger: Class starting in 15 minutes  
- **Recipients**: Enrolled students, instructor
- **Dashboard Display**:
  - Current class card highlighted with "Starting Soon" badge
  - Browser notification if permissions granted
- **Content**: "Your Florida D40 class starts in 15 minutes. Join now."

### 2. Student Activity Notifications

#### Trigger: Student joins class
- **Recipients**: Instructor
- **Dashboard Display**:
  - Live activity feed updates
  - Student count updates in real-time
  - Online students list refreshes
- **Content**: "John Smith has joined the Florida D40 class"

#### Trigger: Assignment submitted
- **Recipients**: Instructor
- **Dashboard Display**:
  - Notification in assignments section
  - Pending review counter updates
- **Content**: "Sarah Johnson submitted Assignment 3 for review"

### 3. System Status Notifications

#### Trigger: Zoom connection issue
- **Recipients**: Instructor, support team
- **Dashboard Display**:
  - System status indicator turns red
  - Alert banner appears in classroom interface
- **Content**: "Zoom connection unstable. Support team notified."

#### Trigger: Scheduled maintenance
- **Recipients**: All users
- **Dashboard Display**:
  - Maintenance banner across dashboard
  - Schedule adjustments shown
- **Content**: "System maintenance scheduled tonight 11 PM - 1 AM EST"

## Notification Delivery Channels

### 1. Dashboard Notifications
- **Real-time**: WebSocket or polling updates
- **Persistent**: Stored in database until dismissed
- **Interactive**: Click actions, quick responses
- **Grouped**: Similar notifications combined

### 2. Email Notifications  
- **Immediate**: Critical alerts, system issues
- **Digest**: Daily/weekly summaries
- **Formatted**: HTML templates with action buttons
- **Unsubscribe**: User preference controls

### 3. SMS Notifications (Optional)
- **Critical Only**: System emergencies, urgent changes
- **Opt-in**: User must explicitly enable
- **Rate Limited**: Prevent spam, daily limits
- **Short Format**: Concise message with link

## Testing Strategy

### Unit Tests
```php
// tests/Unit/Services/NotificationServiceTest.php
public function test_sends_notification_to_user()
public function test_marks_notification_as_read()
public function test_respects_user_preferences()
public function test_handles_notification_expiry()
```

### Integration Tests  
```php
// tests/Feature/InstructorNotificationsTest.php
public function test_instructor_receives_student_activity_notifications()
public function test_notification_preferences_are_applied()
public function test_real_time_notification_delivery()
```

### Frontend Tests
```tsx
// resources/js/React/Instructor/__tests__/NotificationBell.test.tsx
test('displays unread notification count')
test('shows notification dropdown on click')  
test('marks notifications as read when clicked')
test('polls for new notifications')
```

## Performance Considerations

### Database Optimization
- **Indexing**: recipient_id, type, created_at, read_at
- **Archiving**: Move old notifications to archive table
- **Cleanup**: Automatic deletion of expired notifications
- **Partitioning**: Partition by date for large datasets

### Caching Strategy
- **Redis**: Cache unread counts and recent notifications
- **Invalidation**: Clear cache on new notifications
- **TTL**: Short-lived cache for real-time data
- **Warming**: Pre-cache frequently accessed data

### Real-time Performance
- **Connection Limits**: Limit concurrent WebSocket connections
- **Rate Limiting**: Prevent notification spam
- **Batching**: Group similar notifications together
- **Filtering**: Client-side filtering to reduce data transfer

## Security Considerations

### Authorization
- **Role-based**: Check user permissions for notification types
- **Data Privacy**: Ensure users only see their notifications
- **Channel Security**: Validate delivery channel permissions
- **API Protection**: Rate limiting on notification endpoints

### Data Validation
- **Input Sanitization**: Clean notification content
- **Type Validation**: Verify notification types and recipients
- **Size Limits**: Prevent overly large notification payloads
- **XSS Prevention**: Escape notification content in frontend

## Monitoring & Analytics

### Notification Metrics
- **Delivery Rates**: Track successful notification delivery
- **Read Rates**: Monitor notification engagement
- **Channel Performance**: Compare effectiveness across channels  
- **User Preferences**: Track preference changes and trends

### System Health
- **Queue Monitoring**: Track notification processing delays
- **Error Rates**: Monitor failed deliveries and reasons
- **Performance Impact**: Measure dashboard load time impact
- **User Satisfaction**: Track notification-related feedback

## Future Enhancements

### Advanced Features
- **Smart Grouping**: AI-powered notification grouping
- **Predictive Notifications**: Anticipate user needs
- **Custom Templates**: User-customizable notification formats
- **Integration APIs**: Third-party service integrations

### Mobile Support
- **Push Notifications**: Mobile app integration
- **Responsive Design**: Optimized mobile notification UI
- **Offline Support**: Queue notifications for offline users
- **Progressive Web App**: PWA notification support

This comprehensive notification system will transform the instructor dashboard from a static interface into a dynamic, real-time communication hub that keeps all stakeholders informed and engaged with school activities.