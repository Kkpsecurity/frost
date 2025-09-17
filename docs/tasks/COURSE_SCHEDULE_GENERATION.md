# Course Date Generation System Design

## Overview
System for generating course schedules automatically and manually through the instructor dashboard admin interface.

## Architecture Components

### 1. Frontend Interface (React)
- **AdminButton Component**: ✅ Implemented - Shows only for sys_admin users
- **Course Creation Modal**: TODO - Form for manual course date creation
- **Auto Generation Dialog**: TODO - Options for automatic schedule generation

### 2. Backend Services (Laravel)

#### CourseScheduleService
```php
<?php
namespace App\Services\Frost\Admin;

class CourseScheduleService
{
    // Manual course date creation
    public function createCourseDateManually(array $data): array
    
    // Automatic course date generation
    public function generateCourseDatesAutomatically(array $params): array
    
    // Validation and conflict checking
    public function validateScheduleConflicts(array $courseDates): array
}
```

#### Auto Generation Parameters
- **Course Template**: Which course to schedule
- **Date Range**: Start and end dates
- **Frequency**: Daily, weekly, custom pattern
- **Time Slots**: Morning, afternoon, evening blocks
- **Instructor Assignment**: Auto-assign or manual selection
- **Capacity Limits**: Max students per session

### 3. Database Structure

#### Existing Tables (Template-Instance Pattern)
- `courses` (templates)
- `course_dates` (scheduled instances)
- `course_units` (class day templates)
- `inst_units` (scheduled class days)

#### New Migration Requirements
```sql
-- Add scheduling metadata to course_dates
ALTER TABLE course_dates ADD COLUMN generation_method ENUM('manual', 'auto') DEFAULT 'manual';
ALTER TABLE course_dates ADD COLUMN generation_batch_id VARCHAR(50) NULL;
ALTER TABLE course_dates ADD COLUMN auto_generation_params JSON NULL;
```

### 4. API Endpoints

#### Course Creation Routes
```php
// Manual creation
Route::post('/admin/courses/schedule/manual', [CourseScheduleController::class, 'createManual']);

// Auto generation
Route::post('/admin/courses/schedule/auto', [CourseScheduleController::class, 'generateAuto']);

// Get available courses for scheduling
Route::get('/admin/courses/templates', [CourseScheduleController::class, 'getTemplates']);

// Get instructor availability
Route::get('/admin/instructors/availability', [CourseScheduleController::class, 'getInstructorAvailability']);
```

## User Flow

### Manual Course Schedule Creation
1. Admin clicks "Create New Course Schedule" button
2. Modal opens with form:
   - Course selection dropdown
   - Date picker
   - Time selection
   - Instructor assignment
   - Capacity settings
   - Location/classroom
3. Validation and conflict checking
4. Course date created with associated inst_units

### Automatic Course Schedule Generation
1. Admin clicks "Create New Course Schedule" button
2. Modal with tabs: "Manual" | "Auto Generation"
3. Auto tab includes:
   - Course template selection
   - Date range picker
   - Frequency pattern (daily, weekly, custom)
   - Time slot preferences
   - Instructor assignment rules
   - Capacity and location settings
4. Preview generated schedule
5. Confirm and create batch

## Technical Implementation Plan

### Phase 1: Frontend Modal Component ⏳
- Create CourseScheduleModal component
- Add manual creation form
- Integrate with existing AdminButton

### Phase 2: Backend Manual Creation ⏳
- Create CourseScheduleController
- Implement CourseScheduleService for manual creation
- Add validation and conflict checking
- Create API endpoints

### Phase 3: Auto Generation System ⏳
- Extend CourseScheduleService with auto generation
- Add batch processing capabilities
- Implement scheduling algorithms
- Add preview functionality

### Phase 4: Advanced Features ⏳
- Instructor availability integration
- Classroom/resource booking
- Conflict resolution suggestions
- Bulk edit capabilities

## Security Considerations

### Role-Based Access Control
- Only sys_admin can access course scheduling
- Audit logging for all schedule changes
- Validation of instructor assignments

### Data Validation
- Date range validation
- Capacity limit enforcement
- Instructor availability checking
- Resource conflict prevention

## Integration Points

### Existing Systems
- **RoleManager**: ✅ Already integrated for sys_admin checking
- **CourseAuthService**: For instructor assignments
- **ClassroomQueries**: For existing schedule checking
- **Template-Instance Pattern**: ✅ Already documented

### Future Enhancements
- Calendar integration
- Email notifications for new schedules
- Student enrollment automation
- Resource management integration

## Success Metrics
- Reduced manual scheduling time
- Improved schedule consistency
- Fewer scheduling conflicts
- Better instructor utilization

---

*Document Version: 1.0*  
*Created: September 16, 2025*  
*Author: GitHub Copilot*
