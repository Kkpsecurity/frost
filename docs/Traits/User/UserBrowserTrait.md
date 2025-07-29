# UserBrowserTrait

**File:** `app/Models/Traits/User/UserBrowserTrait.php`  
**Namespace:** `App\Models\Traits\User`

## Overview

The UserBrowserTrait provides browser information tracking for users. It stores and manages user agent strings to help with analytics, debugging, and user experience optimization. The trait handles browser information updates efficiently and includes input sanitization for security.

## Dependencies

```php
use App\Models\UserBrowser;
use App\Helpers\TextTk;
```

## Methods

### `SetBrowser(string $browser = ''): void`

**Parameters:**
- `$browser` - User agent string or browser identifier (default: empty string)

**Description:** Sets or updates the user's browser information. Includes input sanitization and efficient update logic to avoid unnecessary database operations.

**Features:**
- Input sanitization via `TextTk::Sanitize()`
- Duplicate detection to prevent unnecessary updates
- Automatic UserBrowser record creation if none exists
- Graceful handling of empty/invalid input

```php
public function SetBrowser(string $browser = ''): void
{
    if (! $browser = TextTk::Sanitize($browser)) {
        // Sanitizer returned empty string - invalid input
        return;
    }

    if ($UserBrowser = $this->UserBrowser) {
        // Update existing record only if changed
        if ($UserBrowser->browser != $browser) {
            $UserBrowser->update(['browser' => $browser]);
        }
    } else {
        // Create new UserBrowser record
        $this->UserBrowser = UserBrowser::create([
            'user_id' => $this->id,
            'browser' => $browser,
        ]);
    }
}
```

## Usage Patterns

### Basic Browser Tracking

```php
$user = Auth::user();

// Set browser from request
$userAgent = $request->header('User-Agent');
$user->SetBrowser($userAgent);

// Set custom browser identifier
$user->SetBrowser('Chrome/91.0.4472.124');
```

### Middleware Integration

```php
class BrowserTrackingMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $userAgent = $request->header('User-Agent');
            
            $user->SetBrowser($userAgent);
        }
        
        return $next($request);
    }
}

// Register in Kernel.php
protected $middlewareGroups = [
    'web' => [
        // ... other middleware
        \App\Http\Middleware\BrowserTrackingMiddleware::class,
    ],
];
```

### Controller Usage

```php
class AuthController extends Controller
{
    public function login(Request $request)
    {
        // ... authentication logic ...
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Track browser on successful login
            $user->SetBrowser($request->header('User-Agent'));
            
            return redirect()->intended('/dashboard');
        }
        
        return back()->withErrors(['email' => 'Invalid credentials']);
    }
}
```

### Event Listener Integration

```php
// In EventServiceProvider
protected $listen = [
    'Illuminate\Auth\Events\Login' => [
        'App\Listeners\TrackUserBrowser',
    ],
];

// Listener implementation
class TrackUserBrowser
{
    public function handle(Login $event)
    {
        $user = $event->user;
        $userAgent = request()->header('User-Agent');
        
        $user->SetBrowser($userAgent);
    }
}
```

## Security Features

### Input Sanitization

The trait uses `TextTk::Sanitize()` to clean browser strings:

```php
if (! $browser = TextTk::Sanitize($browser)) {
    return; // Exit if sanitization fails
}
```

**Security Benefits:**
- Removes potentially malicious HTML/JavaScript
- Strips dangerous characters
- Prevents XSS attacks through user agent manipulation
- Handles encoding issues gracefully

### Data Validation

```php
// Conceptual validation in TextTk::Sanitize()
public static function Sanitize($input)
{
    // Remove HTML tags
    $clean = strip_tags($input);
    
    // Remove dangerous characters
    $clean = preg_replace('/[<>"\']/', '', $clean);
    
    // Trim whitespace
    $clean = trim($clean);
    
    // Return null if empty after sanitization
    return empty($clean) ? null : $clean;
}
```

## Performance Optimization

### Duplicate Detection

The trait includes smart update logic to prevent unnecessary database writes:

```php
if ($UserBrowser->browser != $browser) {
    $UserBrowser->update(['browser' => $browser]); // Only update if changed
}
```

**Benefits:**
- Reduces database load
- Prevents unnecessary model events
- Maintains audit trail accuracy
- Improves response times

### Relationship Caching

```php
// Efficient relationship usage
if ($UserBrowser = $this->UserBrowser) {
    // Use cached relationship if available
} else {
    // Create new record only when needed
}
```

## Database Relationship

### UserBrowser Model Relationship

The trait assumes a `UserBrowser` relationship exists on the User model:

```php
// In User model
public function UserBrowser()
{
    return $this->hasOne(UserBrowser::class, 'user_id');
}
```

### Database Schema

Expected `user_browsers` table structure:

```sql
CREATE TABLE user_browsers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    browser VARCHAR(500) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_browsers_user_id (user_id)
);
```

## Analytics Integration

### Browser Statistics

```php
// Analytics helper methods (conceptual)
public function getBrowserStats()
{
    return UserBrowser::selectRaw('
        SUBSTRING_INDEX(browser, "/", 1) as browser_name,
        COUNT(*) as user_count
    ')
    ->groupBy('browser_name')
    ->orderBy('user_count', 'desc')
    ->get();
}

// Usage in analytics controller
public function browserStats()
{
    $stats = $this->getBrowserStats();
    
    return view('admin.analytics.browsers', compact('stats'));
}
```

### User Agent Parsing

```php
// Enhanced browser detection (using external library)
use Jenssegers\Agent\Agent;

public function SetBrowserDetails(string $userAgent): void
{
    $this->SetBrowser($userAgent); // Store full user agent
    
    // Parse additional details
    $agent = new Agent();
    $agent->setUserAgent($userAgent);
    
    $details = [
        'browser' => $agent->browser(),
        'version' => $agent->version($agent->browser()),
        'platform' => $agent->platform(),
        'device' => $agent->device(),
        'is_mobile' => $agent->isMobile(),
        'is_desktop' => $agent->isDesktop(),
    ];
    
    // Store in additional fields or JSON column
    $this->UserBrowser->update(['details' => json_encode($details)]);
}
```

## Testing Strategies

### Unit Testing

```php
public function test_sets_browser_information()
{
    $user = User::factory()->create();
    $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
    
    $user->SetBrowser($userAgent);
    
    $this->assertDatabaseHas('user_browsers', [
        'user_id' => $user->id,
        'browser' => $userAgent,
    ]);
}

public function test_updates_existing_browser_when_different()
{
    $user = User::factory()->create();
    $oldAgent = 'Chrome/90.0.4430.212';
    $newAgent = 'Chrome/91.0.4472.124';
    
    // Set initial browser
    UserBrowser::create([
        'user_id' => $user->id,
        'browser' => $oldAgent,
    ]);
    
    // Update with new browser
    $user->SetBrowser($newAgent);
    
    $this->assertDatabaseHas('user_browsers', [
        'user_id' => $user->id,
        'browser' => $newAgent,
    ]);
    
    $this->assertDatabaseMissing('user_browsers', [
        'user_id' => $user->id,
        'browser' => $oldAgent,
    ]);
}

public function test_ignores_empty_browser_string()
{
    $user = User::factory()->create();
    
    $user->SetBrowser('');
    $user->SetBrowser('   ');
    $user->SetBrowser('<script>alert("xss")</script>');
    
    $this->assertDatabaseMissing('user_browsers', [
        'user_id' => $user->id,
    ]);
}

public function test_does_not_update_when_browser_unchanged()
{
    $user = User::factory()->create();
    $userAgent = 'Chrome/91.0.4472.124';
    
    // Create initial record
    $browser = UserBrowser::create([
        'user_id' => $user->id,
        'browser' => $userAgent,
    ]);
    
    $originalUpdatedAt = $browser->updated_at;
    
    // Wait a moment then set same browser
    sleep(1);
    $user->SetBrowser($userAgent);
    
    // Should not update timestamp
    $this->assertEquals(
        $originalUpdatedAt->timestamp,
        $browser->fresh()->updated_at->timestamp
    );
}
```

### Integration Testing

```php
public function test_browser_tracking_middleware()
{
    $user = User::factory()->create();
    $userAgent = 'TestBrowser/1.0';
    
    $this->actingAs($user)
         ->withHeaders(['User-Agent' => $userAgent])
         ->get('/dashboard');
    
    $this->assertDatabaseHas('user_browsers', [
        'user_id' => $user->id,
        'browser' => $userAgent,
    ]);
}

public function test_login_tracking()
{
    $user = User::factory()->create(['password' => Hash::make('password')]);
    $userAgent = 'LoginTestBrowser/1.0';
    
    $this->withHeaders(['User-Agent' => $userAgent])
         ->post('/login', [
             'email' => $user->email,
             'password' => 'password',
         ]);
    
    $this->assertDatabaseHas('user_browsers', [
        'user_id' => $user->id,
        'browser' => $userAgent,
    ]);
}
```

## Common Use Cases

### Security Monitoring

```php
// Detect unusual browser changes
public function detectBrowserChange(): bool
{
    $currentBrowser = $this->UserBrowser?->browser;
    $sessionBrowser = session('browser_fingerprint');
    
    return $currentBrowser && $sessionBrowser && 
           $currentBrowser !== $sessionBrowser;
}

// Usage in security middleware
if ($user->detectBrowserChange()) {
    Log::warning('Browser change detected', [
        'user_id' => $user->id,
        'old_browser' => session('browser_fingerprint'),
        'new_browser' => $user->UserBrowser->browser,
    ]);
}
```

### User Experience Optimization

```php
// Customize interface based on browser
public function isMobileBrowser(): bool
{
    $browser = $this->UserBrowser?->browser ?? '';
    
    return str_contains($browser, 'Mobile') || 
           str_contains($browser, 'Android') ||
           str_contains($browser, 'iPhone');
}

// Usage in controller
public function dashboard()
{
    $user = Auth::user();
    $isMobile = $user->isMobileBrowser();
    
    return view($isMobile ? 'mobile.dashboard' : 'desktop.dashboard');
}
```

### Support and Debugging

```php
// Include browser info in support tickets
public function createSupportTicket(array $data): SupportTicket
{
    $ticketData = array_merge($data, [
        'user_id' => $this->id,
        'browser_info' => $this->UserBrowser?->browser,
        'user_agent' => request()->header('User-Agent'),
    ]);
    
    return SupportTicket::create($ticketData);
}
```

## Error Handling

The trait handles various error conditions gracefully:

1. **Empty Input**: Returns early without database operations
2. **Sanitization Failure**: Exits when `TextTk::Sanitize()` returns falsy
3. **Missing Relationship**: Creates new UserBrowser record automatically
4. **Database Errors**: Relies on Laravel's exception handling

## Related Models

- [UserBrowser](../Models/UserBrowser.md) - Browser information storage
- [User](../Models/User.md) - User model that uses this trait

## Related Components

- `TextTk::Sanitize()` - Input sanitization utility
- Laravel's Request handling
- Session management for browser fingerprinting

---

*Last updated: July 28, 2025*
