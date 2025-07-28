# Traits Documentation

This directory contains detailed documentation for all traits used in the Frost application's models.

## Overview

The Frost application uses traits to organize shared functionality across models. Traits provide a clean way to share methods between classes while maintaining separation of concerns.

## Trait Standards & Patterns

### Documentation Format
Each trait follows a standard PHPDoc format with:
- `@file` - Trait filename
- `@brief` - Short description of the trait's purpose
- `@details` - Detailed description of functionality and methods

### Common Patterns
- **Session Management** - Caching data in user sessions for performance
- **Role-based Logic** - Hierarchical permission checking
- **Database Abstraction** - Simplified access to related models
- **Input Sanitization** - Consistent data cleaning

### Naming Conventions
- Traits are suffixed with `Trait` (e.g., `UserPrefsTrait`)
- Methods use descriptive names following Laravel conventions
- Constants use UPPER_SNAKE_CASE format

## Trait Categories

### User-Related Traits (5 traits)
Located in `app/Models/Traits/User/`:

- **[CourseAuthsTrait](User/CourseAuthsTrait.md)** - Course enrollment and authorization management
- **[UserPrefsTrait](User/UserPrefsTrait.md)** - User preferences with session caching
- **[RolesTrait](User/RolesTrait.md)** - Role-based permission checking and routing
- **[ExamsTrait](User/ExamsTrait.md)** - Exam authorization and access
- **[UserBrowserTrait](User/UserBrowserTrait.md)** - Browser tracking and management

### CourseAuth-Related Traits (6 traits)
Located in `app/Models/Traits/CourseAuth/`:

- **[ClassroomButton](CourseAuth/ClassroomButton.md)** - Classroom access control logic
- **[ClassroomCourseDate](CourseAuth/ClassroomCourseDate.md)** - Scheduled class date management
- **[ExamsTrait](CourseAuth/ExamsTrait.md)** - Course-level exam authorization
- **[LastInstructor](CourseAuth/LastInstructor.md)** - Instructor tracking and assignment
- **[LessonsTrait](CourseAuth/LessonsTrait.md)** - Lesson progress and management
- **[SetStartDateTrait](CourseAuth/SetStartDateTrait.md)** - Course start date management

### Order-Related Traits (3 traits)
Located in `app/Models/Traits/Order/`:

- **[CalcPrice](Order/CalcPrice.md)** - Order pricing calculations with discounts
- **[DiscountCodeTrait](Order/DiscountCodeTrait.md)** - Discount code application logic
- **[SetCompleted](Order/SetCompleted.md)** - Order completion workflow

### StudentLesson-Related Traits (2 traits)
Located in `app/Models/Traits/StudentLesson/`:

- **[ClearDNC](StudentLesson/ClearDNC.md)** - Clear "Do Not Continue" status
- **[SetUnitCompleted](StudentLesson/SetUnitCompleted.md)** - Unit completion logic

### InstLesson-Related Traits (2 traits)
Located in `app/Models/Traits/InstLesson/`:

- **[GetCourseUnitLesson](InstLesson/GetCourseUnitLesson.md)** - Course unit lesson retrieval
- **[InstCanClose](InstLesson/InstCanClose.md)** - Instructor lesson closing logic

### InstUnit-Related Traits (1 trait)
Located in `app/Models/Traits/InstUnit/`:

- **[InstWaitNextLesson](InstUnit/InstWaitNextLesson.md)** - Next lesson waiting logic

### Usage in Models
These traits are typically used in the `User` model to extend its functionality:

```php
class User extends Authenticatable
{
    use CourseAuthsTrait;
    use UserPrefsTrait;
    use RolesTrait;
    use ExamsTrait;
    use UserBrowserTrait;
    
    // ... model definition
}
```

## Common Features

### Performance Optimization
- **Session Caching**: `UserPrefsTrait` caches preferences in session
- **Lazy Loading**: Relationships loaded only when needed
- **Query Optimization**: Efficient database queries with proper indexing

### Security Features
- **Input Sanitization**: All user input sanitized via `TextTk::Sanitize()`
- **Authorization Checks**: Role-based access control
- **Session Validation**: User identity verification before operations

### Error Handling
- **Graceful Degradation**: Methods handle missing data gracefully
- **Default Values**: Sensible defaults when data unavailable
- **Null Safety**: Proper null checking throughout

## Architecture Benefits

### Separation of Concerns
Each trait handles a specific aspect of user functionality:
- Course management separate from preferences
- Role logic isolated from browser tracking
- Clear boundaries between features

### Code Reusability
Traits can be mixed and matched across different models as needed:
- Common functionality shared without inheritance
- Easy to test individual features
- Modular design enables flexible composition

### Maintainability
- Single responsibility principle followed
- Easy to locate specific functionality
- Clear interfaces between components

## File Structure

```
docs/Traits/
├── README.md                           # This overview file
├── SUMMARY.md                          # Comprehensive trait inventory
├── User/
│   ├── CourseAuthsTrait.md            # Course authorization
│   ├── UserPrefsTrait.md              # User preferences
│   ├── RolesTrait.md                  # Role management
│   ├── ExamsTrait.md                  # Exam access
│   └── UserBrowserTrait.md            # Browser tracking
├── CourseAuth/
│   ├── ClassroomButton.md             # Classroom access control
│   ├── ClassroomCourseDate.md         # Scheduled class management
│   ├── ExamsTrait.md                  # Course exam authorization
│   ├── LastInstructor.md              # Instructor tracking
│   ├── LessonsTrait.md                # Lesson management
│   └── SetStartDateTrait.md           # Start date management
├── Order/
│   ├── CalcPrice.md                   # Price calculations
│   ├── DiscountCodeTrait.md           # Discount code logic
│   └── SetCompleted.md                # Order completion
├── StudentLesson/
│   ├── ClearDNC.md                    # Clear Do Not Continue
│   └── SetUnitCompleted.md            # Unit completion
├── InstLesson/
│   ├── GetCourseUnitLesson.md         # Course unit lesson retrieval
│   └── InstCanClose.md                # Instructor lesson closing
└── InstUnit/
    └── InstWaitNextLesson.md          # Next lesson waiting logic
```

## Development Guidelines

### Adding New Traits
When creating new traits:
1. Follow the established naming convention
2. Include comprehensive PHPDoc headers
3. Implement proper error handling
4. Add corresponding documentation
5. Write unit tests for all methods

### Testing Traits
Traits should be tested in isolation:
```php
// Test trait functionality independently
$user = new class {
    use UserPrefsTrait;
    public $id = 1;
};
```

### Integration Points
Traits integrate with the broader application through:
- Laravel's session management
- Eloquent relationships
- Route definitions
- Middleware and policies

---

*Generated on: July 28, 2025*  
*Application: Frost Learning Management System*  
*Total Traits Documented: 5*
