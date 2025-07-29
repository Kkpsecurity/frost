# Course Model

**File:** `app/Models/Course.php`  
**Table:** `courses`  
**Primary Key:** `id`  
**Timestamps:** No

## Overview

The Course model represents individual courses in the Frost Learning Management System. It includes course details, pricing, scheduling, and relationships with units, exams, and authentication records. The model implements expiration handling, caching, and observability features.

## Attributes

### Database Fields

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| `id` | integer | No | Auto | Primary key |
| `is_active` | boolean | No | true | Course availability status |
| `exam_id` | integer | Yes | - | Foreign key to exams table |
| `eq_spec_id` | integer | Yes | - | Foreign key to exam question specs |
| `title` | varchar(64) | No | - | Course title (short) |
| `title_long` | text | Yes | - | Course title (detailed) |
| `price` | decimal(8,2) | Yes | - | Course price |
| `total_minutes` | integer | Yes | - | Total course duration in minutes |
| `policy_expire_days` | integer | Yes | - | Access expiration policy in days |
| `dates_template` | json | Yes | - | Course date template configuration |
| `zoom_creds_id` | integer | Yes | - | Foreign key to Zoom credentials |
| `needs_range` | boolean | No | false | Whether course requires date range |

### Mass Assignment
**Guarded:** `['id']` (only ID is protected)

### Default Values
```php
protected $attributes = [
    'is_active' => true,
    'needs_range' => false,
];
```

## Type Casting

```php
protected $casts = [
    'id' => 'integer',
    'is_active' => 'boolean',
    'exam_id' => 'integer',
    'eq_spec_id' => 'integer',
    'title' => 'string',        // Max 64 characters
    'title_long' => 'string',   // Text field
    'price' => 'decimal:2',     // 2 decimal places
    'total_minutes' => 'integer',
    'policy_expire_days' => 'integer',
    'dates_template' => JSONCast::class, // Custom JSON cast
    'zoom_creds_id' => 'integer',
    'needs_range' => 'boolean',
];
```

## Traits Used

- `ExpirationTrait` - Handles course access expiration logic
- `Observable` - Event handling and model observation
- `RCacheModelTrait` - Automated caching functionality

## Relationships

### Has Many
- **CourseAuths()** → `CourseAuth` - Course authorization records
- **CourseUnits()** → `CourseUnit` - Course units and structure

### Belongs To
- **Exam()** → `Exam` - Associated examination
- **ExamQuestionSpec()** → `ExamQuestionSpec` - Exam question specifications
- **ZoomCreds()** → `ZoomCreds` - Zoom integration credentials

## Methods

### Display Methods

#### `__toString(): string`
Returns the course title when the model is cast to string.

#### `ShortTitle(): string`
Returns the course title with parenthetical content removed.
```php
// "Advanced PHP (2024)" → "Advanced PHP"
$course->ShortTitle();
```

#### `LongTitle(): string`
Returns the long title with parenthetical content removed.
```php
$course->LongTitle();
```

### Mutators (Input Sanitization)

#### `setTitleAttribute($value)`
Sanitizes the course title before saving.
- Uses `TextTk::Sanitize()` to clean input
- Removes potentially harmful content

#### `setTitleLongAttribute($value)`
Sanitizes the long course title before saving.
- Uses `TextTk::Sanitize()` to clean input
- Ensures safe storage of detailed descriptions

## Special Features

### Environment-Aware Zoom Integration
The `ZoomCreds()` relationship includes development environment handling:
```php
public function ZoomCreds()
{
    // Force admin Zoom account in non-production
    if (! app()->environment('production')) {
        $this->zoom_creds_id = 1;
    }
    
    return $this->belongsTo(ZoomCreds::class, 'zoom_creds_id');
}
```

### JSON Template System
The `dates_template` field uses a custom `JSONCast` for complex date configuration storage.

## Usage Examples

### Creating a Course
```php
$course = Course::create([
    'title' => 'Introduction to Laravel',
    'title_long' => 'Introduction to Laravel Framework Development',
    'price' => 199.99,
    'total_minutes' => 480,
    'policy_expire_days' => 365,
]);
```

### Working with Relationships
```php
$course = Course::find(1);

// Get course units
$units = $course->CourseUnits;

// Get course authorization records
$auths = $course->CourseAuths;

// Check if course has an exam
if ($course->exam_id) {
    $exam = $course->Exam;
}

// Get Zoom credentials
$zoomCreds = $course->ZoomCreds;
```

### Title Formatting
```php
$course = Course::find(1);

echo $course->title;       // "Advanced PHP (2024)"
echo $course->ShortTitle(); // "Advanced PHP"
echo $course->LongTitle();  // "Advanced PHP Programming"
```

## Business Logic

### Pricing
- Supports decimal pricing with 2-digit precision
- Nullable to allow free courses

### Expiration Policy
- `policy_expire_days` defines access duration
- Integrated with `ExpirationTrait` for automatic handling

### Course Structure
- Courses contain multiple `CourseUnit` records
- Units define the learning progression
- Total minutes provide duration estimates

## Security Features

- Input sanitization on title fields via `TextTk::Sanitize()`
- Mass assignment protection via `$guarded`
- Environment-aware development overrides

## Performance Considerations

- No timestamps for reduced overhead
- Caching via `RCacheModelTrait`
- JSON template storage for flexible configuration
- Indexed foreign keys for efficient joins

## Related Models

- [CourseUnit](CourseUnit.md) - Course structure and units
- [CourseAuth](CourseAuth.md) - Authorization and access control
- [CourseDate](CourseDate.md) - Course scheduling
- [Exam](Exam.md) - Associated examinations
- [ZoomCreds](ZoomCreds.md) - Video conferencing integration
- [Order](Order.md) - Purchase and enrollment records

---

*Last updated: July 28, 2025*
