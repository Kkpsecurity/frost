# Model Documentation

This directory contains detailed documentation for all Eloquent models in the Frost application.

## Overview

The Frost application uses a comprehensive set of models to manage educational content, user management, and system configuration. All models follow consistent patterns and conventions.

## Model Standards & Patterns

### Documentation Format
Each model follows a standard PHPDoc format with:
- `@file` - Model filename
- `@brief` - Short description of the model's purpose
- `@details` - Detailed description of functionality and relationships

### Common Traits Used
- **HasCompositePrimaryKey** - For models with composite primary keys
- **ExpirationTrait** - For models with expiration functionality
- **Observable** - For event handling
- **RCacheModelTrait** - For caching functionality
- **StaticModel** - For read-only reference data

### Standard Properties
- `$table` - Database table name
- `$primaryKey` - Primary key field(s)
- `$timestamps` - Whether to use created_at/updated_at
- `$casts` - Type casting for attributes
- `$fillable` - Mass assignable attributes
- `$guarded` - Protected attributes

## Model Categories

### Core User Management
- [User](User.md) - Main user authentication and profile
- [Role](Role.md) - User roles and permissions
- [UserPref](UserPref.md) - User preferences storage
- [UserBrowser](UserBrowser.md) - User browser tracking
- [Admin](Admin.md) - Administrative users

### Course Management
- [Course](Course.md) - Course definitions and settings
- [CourseAuth](CourseAuth.md) - Course authorization
- [CourseDate](CourseDate.md) - Course scheduling
- [CourseUnit](CourseUnit.md) - Course unit structure
- [CourseUnitLesson](CourseUnitLesson.md) - Unit-lesson relationships

### Educational Content
- [Lesson](Lesson.md) - Individual lesson content
- [InstLesson](InstLesson.md) - Instructor lessons
- [SelfStudyLesson](SelfStudyLesson.md) - Self-study content
- [StudentLesson](StudentLesson.md) - Student lesson progress
- [StudentUnit](StudentUnit.md) - Student unit progress

### Assessment System
- [Exam](Exam.md) - Examination definitions
- [ExamAuth](ExamAuth.md) - Exam authorization
- [ExamQuestion](ExamQuestion.md) - Individual exam questions
- [ExamQuestionSpec](ExamQuestionSpec.md) - Question specifications
- [Challenge](Challenge.md) - Challenge assessments

### E-commerce
- [Order](Order.md) - Purchase orders
- [PaymentType](PaymentType.md) - Payment method types
- [DiscountCode](DiscountCode.md) - Discount and promotion codes

### System Configuration
- [SiteConfig](SiteConfig.md) - Global site configuration
- [Range](Range.md) - Date/time ranges
- [RangeDate](RangeDate.md) - Range date specifications
- [ZoomCreds](ZoomCreds.md) - Zoom integration credentials

### Utility Models
- [ChatLog](ChatLog.md) - Chat message logging
- [Validation](Validation.md) - Data validation rules
- [InstLicense](InstLicense.md) - Instructor licensing
- [InstUnit](InstUnit.md) - Instructor unit assignments

## File Structure

```
docs/Models/
├── README.md                 # This overview file
├── User.md                   # User model documentation
├── Course.md                 # Course model documentation
├── Role.md                   # Role model documentation
└── [ModelName].md            # Individual model documentation
```

## Model Relationships

The models form a complex web of relationships:
- Users have Roles, Preferences, and Browser tracking
- Courses contain Units, which contain Lessons
- Students track progress through Lessons and Units  
- Exams are associated with Courses and contain Questions
- Orders link Users to Courses with Payment information

## Caching Strategy

Many models implement caching through:
- `RCacheModelTrait` for automated caching
- `RCache` service for manual cache management
- Composite keys for efficient lookups

## Security Features

Input sanitization is handled through:
- `TextTk::Sanitize()` for HTML filtering
- Constants like `ALLOW_HTML_*` for configuration
- Proper `$fillable` and `$guarded` arrays

---

*Generated on: July 28, 2025*  
*Application: Frost Learning Management System*
