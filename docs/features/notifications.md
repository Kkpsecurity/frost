# Student Notifications System

## Overview

The student notifications system provides real-time alerts and communications to users throughout their learning journey. Notifications are delivered through multiple channels (in-app, email, browser push) and can be customized by users through their notification preferences.

## Architecture

### Configuration

- **Config File**: `config/user_notifications.php`
- **Notification Classes**: `app/Notifications/`
- **User Preferences**: Stored in `user_prefs` table
- **Notification Data**: Stored in `notifications` table (Laravel default)

### Channels

- **Database** - In-app notifications (always enabled for critical notifications)
- **Mail** - Email notifications (user controllable)
- **Browser** - Browser push notifications (planned)
- **SMS** - SMS notifications (future feature)

### Notification Metadata Structure

```php
[
    'title' => 'Notification Title',
    'message' => 'Notification message content',
    'icon' => 'font-awesome-icon-name',
    'priority_color' => 'bootstrap-color', // primary, success, warning, danger, info
    'url' => 'https://example.com/action', // Optional redirect URL
    // Additional custom fields as needed
]
```

## User Interface Components

### Header Notification Dropdown

**Location**: `resources/views/components/frontend/site/partials/top_right_nav.blade.php`

Features:

- Bell icon with unread count badge
- Dropdown showing last 6 notifications
- Mark all as read button
- Individual notification items with:
    - Icon with priority color
    - Title and message
    - Relative timestamp
    - Unread highlighting
- View all notifications link
- Empty state when no notifications

### Notification Preferences Page

**Location**: `resources/views/frontend/account/sections/notifications.blade.php`

Features:

- Global channel toggles (In-App, Email, Browser Push)
- Category-based notification controls:
    - Account & Registration
    - Course Enrollment & Purchase
    - Classroom Experience
    - Course Progress & Completion
    - Exams & Assessments
    - System & Administrative
- Individual notification toggle switches
- Priority badges
- Channel indicators
- Save preferences button

## Implemented Notifications

### Phase 8: Account & Registration ✅

#### WelcomeNotification

**File**: `app/Notifications/Account/WelcomeNotification.php`

- **Trigger**: User registration
- **Channels**: Database, Mail
- **Priority**: High
- **User Controllable**: No (critical)
- **When Sent**: Immediately after account creation in `RegisteredUserController@store`
- **Email Content**:
    - Welcome message with platform overview
    - Next steps (complete profile, verify email, browse courses)
    - Links to dashboard and courses
- **Database Content**:
    - Title: "Welcome to {app_name}!"
    - Message: Account creation confirmation
    - Icon: user-check
    - Priority Color: success

#### EmailVerifiedNotification

**File**: `app/Notifications/Account/EmailVerifiedNotification.php`

- **Trigger**: Email verification
- **Channels**: Database, Mail
- **Priority**: Medium
- **User Controllable**: No (critical)
- **When Sent**: Via `SendEmailVerifiedNotification` listener when `Verified` event fires
- **Event Listener**: `app/Listeners/SendEmailVerifiedNotification.php`
- **Email Content**:
    - Confirmation of email verification
    - Full access granted message
    - Feature list (enroll, receive notifications, access materials)
    - Dashboard link
- **Database Content**:
    - Title: "Email Verified Successfully"
    - Message: Full access confirmation
    - Icon: envelope-circle-check
    - Priority Color: success

#### ProfileIncompleteNotification

**File**: `app/Notifications/Account/ProfileIncompleteNotification.php`

- **Trigger**: Manual check for missing profile fields
- **Channels**: Database, Mail (if user enabled)
- **Priority**: High
- **User Controllable**: Yes
- **Constructor Parameter**: `array $missingFields` - List of incomplete fields
- **Email Content**:
    - List of missing fields
    - Importance explanation
    - Profile completion link
- **Database Content**:
    - Title: "Complete Your Profile"
    - Message: Lists missing fields
    - Icon: user-edit
    - Priority Color: warning
    - Additional Data: `missing_fields` array

#### ProfileUpdatedNotification

**File**: `app/Notifications/Account/ProfileUpdatedNotification.php`

- **Trigger**: Profile update submission
- **Channels**: Database only
- **Priority**: Low
- **User Controllable**: Yes
- **When Sent**: After successful profile update in `ProfileController@updateProfile`
- **Constructor Parameter**: `array $updatedFields` - List of updated fields
- **Database Content**:
    - Title: "Profile Updated Successfully"
    - Message: Confirmation of update
    - Icon: check-circle
    - Priority Color: success
    - Additional Data: `updated_fields` array

## Routes & Controllers

### Notification Management Routes

**File**: `routes/web.php`

```php
// Mark all notifications as read
POST /notifications/mark-all-read
→ ProfileController@markAllNotificationsRead

// Mark single notification as read and redirect
GET /notifications/{notification}/read
→ ProfileController@markNotificationRead

// Update notification preferences
POST /account/notifications
→ ProfileController@updateNotifications
```

### Controller Methods

**File**: `app/Http/Controllers/Student/ProfileController.php`

#### markAllNotificationsRead()

- Marks all unread notifications as read for authenticated user
- Returns back with success message
- Used by "Mark all read" button in header dropdown

#### markNotificationRead($notificationId)

- Marks specific notification as read
- Redirects to notification's URL if available
- Falls back to notifications page
- Used when clicking individual notifications

#### updateNotifications(Request $request)

- Updates user notification preferences
- Validates user-controllable notifications only
- Saves channel preferences (database, mail, browser)
- Saves individual notification toggles
- Redirects back with success message

## User Preferences System

### Storage

User preferences are stored in the `user_prefs` table with the following naming convention:

```php
// Channel preferences
notification_channel_database = '1' or '0'
notification_channel_mail = '1' or '0'
notification_channel_browser = '1' or '0'

// Individual notifications (using config key)
notification_account.welcome = '1' or '0'
notification_account.profile_incomplete = '1' or '0'
notification_account.profile_updated = '1' or '0'
// etc...
```

### Default Behavior

- All notifications enabled by default
- Critical notifications cannot be disabled (user_controllable = false)
- User preferences checked in notification's `via()` method

### Preference Checking Example

```php
public function via(object $notifiable): array
{
    $channels = ['database'];

    $userPrefs = $notifiable->UserPrefs->pluck('pref_value', 'pref_name')->toArray();

    // Check if user enabled this notification type
    if (($userPrefs['notification_account.profile_updated'] ?? '1') === '1') {
        // Check if mail channel is enabled
        if (($userPrefs['notification_channel_mail'] ?? '1') === '1') {
            $channels[] = 'mail';
        }
    }

    return $channels;
}
```

## Notification Data Access

### In Blade Templates

```php
// Get unread count
$unreadCount = Auth::user()->unreadNotifications->count();

// Get recent notifications
$notifications = Auth::user()->notifications()->take(6)->get();

// Access notification data
$notification->data['title']
$notification->data['message']
$notification->data['icon']
$notification->data['priority_color']
$notification->data['url']

// Check if read
is_null($notification->read_at) // true = unread
```

### In Controllers

```php
// Send notification
$user->notify(new WelcomeNotification());

// Mark as read
$notification->markAsRead();

// Mark all as read
$user->unreadNotifications->markAsRead();
```

## Email Templates

All notification emails use Laravel's MailMessage builder with:

- Custom subject line
- Greeting with user's first name
- Multi-line content with bullet points
- Primary action button
- Secondary action links (optional)
- Custom salutation

Example structure:

```php
return (new MailMessage)
    ->subject('Welcome to ' . config('app.name') . '!')
    ->greeting('Welcome, ' . $firstName . '!')
    ->line('Main content line 1')
    ->line('• Bullet point 1')
    ->line('• Bullet point 2')
    ->action('Primary Action', $url)
    ->line('Additional information')
    ->salutation('Best regards, The ' . config('app.name') . ' Team');
```

## Queue Configuration

All notifications implement `ShouldQueue` interface for asynchronous processing:

- Prevents blocking user requests
- Improves response time
- Handles email delivery failures gracefully
- Uses Laravel's queue system

Make sure queue worker is running:

```bash
php artisan queue:work
```

## Testing

### Manual Testing Checklist

**Registration Flow:**

- [ ] Register new user → Welcome notification appears
- [ ] Check email → Welcome email received
- [ ] Header bell icon shows unread badge (1)
- [ ] Click notification → Redirects to dashboard
- [ ] Notification marked as read

**Email Verification:**

- [ ] Verify email → Email verified notification appears
- [ ] Check email → Verification confirmation received
- [ ] Unread count increases

**Profile Updates:**

- [ ] Update profile → Profile updated notification appears
- [ ] Notification shows in dropdown
- [ ] No email sent (database only)

**Notification Preferences:**

- [ ] Navigate to Account → Notifications
- [ ] Toggle channel preferences → Saves successfully
- [ ] Toggle individual notifications → Saves successfully
- [ ] Disable email → No emails sent for disabled notifications
- [ ] Critical notifications cannot be disabled

**Notification Dropdown:**

- [ ] Click bell icon → Dropdown opens
- [ ] Shows last 6 notifications
- [ ] Unread notifications highlighted
- [ ] Click "Mark all read" → All marked as read, badge disappears
- [ ] Click individual notification → Redirects correctly
- [ ] Empty state shows when no notifications

## Future Enhancements

### Phase 1: Payment & Billing ✅ COMPLETE

**Status**: Implemented with Stripe/PayPal integration

Notifications:

- Payment success confirmations ✅
- Payment failure alerts ✅
- Payment processing updates ✅
- Payment method added/removed ✅
- Default payment method changed ✅
- Card expiring reminders (scheduled job needed)
- Invoice generated ✅
- Receipt emailed ✅
- Refund initiated (ready for integration)
- Refund processed (ready for integration)
- Balance due reminders (trigger logic needed)

**Integration Points**:

- `app/Http/Controllers/Student/ProfileController.php` - Payment method CRUD ✅
- Event-driven: `PaymentCompleted`, `PaymentFailed`, `PaymentMethodAdded`, etc. ✅
- 6 event listeners handle notification dispatching ✅
- All notifications respect user preferences ✅
- Critical notifications (failures, refunds) always sent ✅

**Webhook Integration Needed**:

- Stripe: `payment_intent.succeeded`, `payment_intent.payment_failed`, `charge.refunded`
- PayPal: Payment completion and refund events

### Phase 2: Course Enrollment & Purchase

- Order created notifications
- Course enrolled notifications
- Course start date notifications
- Materials available notifications
- Discount code applied notifications

### Phase 3: Exams & Assessments

- Exam authorized notifications
- Exam time warnings (15 min, 5 min)
- Exam completed notifications
- Pass/fail notifications
- Retake available notifications

### Phase 4: Identity Verification

- Verification required notifications
- Upload confirmation notifications
- Approval/rejection notifications
- Resubmit prompts

### Phase 5: Pre-Classroom Preparation

- Terms agreement reminders
- Classroom rules reminders
- Class starting reminders (tomorrow, 1 hour)
- Schedule change alerts

### Phase 6: Classroom Experience

- Session started notifications
- Lesson completed notifications
- Instructor messages
- Chat mentions
- Progress updates

### Phase 7: Course Progress & Completion

- Milestone notifications (25%, 50%, 75%, 90%)
- Course completion notifications
- Certificate ready notifications
- Expiration warnings (30 days, 7 days)

### Phase 9: Profile & Account Management

- Password changed notifications
- New device login alerts
- Suspicious activity warnings
- Account status changes

## Best Practices

1. **Always queue notifications** - Implement `ShouldQueue`
2. **Check user preferences** - Respect user's channel and notification settings
3. **Provide deep links** - Include `url` in notification data
4. **Use appropriate icons** - Match FontAwesome icons to notification type
5. **Set correct priority** - Use priority colors consistently
6. **Write clear messages** - Keep notification messages concise and actionable
7. **Test email templates** - Verify email rendering across clients
8. **Handle failures gracefully** - Queue system will retry failed notifications
9. **Track important data** - Include relevant metadata in notification data array
10. **Document new notifications** - Update this file when adding notifications

## Troubleshooting

### Notifications not appearing

- Check queue worker is running: `php artisan queue:work`
- Verify notification sent: Check `notifications` table
- Check user preferences: Ensure notification not disabled

### Emails not sending

- Verify mail configuration in `.env`
- Check mail channel enabled in user preferences
- Check queue jobs table for failures
- Review `storage/logs/laravel.log`

### Badge count incorrect

- Clear browser cache
- Check `read_at` column in notifications table
- Verify unread count query: `$user->unreadNotifications->count()`

### Dropdown not working

- Check Bootstrap JS loaded
- Verify dropdown markup correct
- Check browser console for errors
- Ensure route exists: `notifications.mark-read`

## Related Files

- Configuration: `config/user_notifications.php`
- Notification Classes: `app/Notifications/Account/`
- Event Listeners: `app/Listeners/`
- Event Provider: `app/Providers/EventServiceProvider.php`
- Controllers: `app/Http/Controllers/Student/ProfileController.php`
- Routes: `routes/web.php`
- Views:
    - Header Dropdown: `resources/views/components/frontend/site/partials/top_right_nav.blade.php`
    - Preferences Page: `resources/views/frontend/account/sections/notifications.blade.php`
- Documentation: `docs/tasks/student-notifications-implementation-phases.md`
