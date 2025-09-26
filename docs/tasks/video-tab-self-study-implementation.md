# Video Tab Self-Study Implementation Task

**Date**: September 19, 2025  
**Status**: Planning Phase  
**Priority**: High - Core self-study functionality

## 📋 Task Overview

Implement the Video tab functionality for self-study mode where students can access lessons independently with comprehensive onboarding validation, lesson player controls, and progress tracking.

## 🎯 Core Requirements

### Self-Study Access Flow
1. **Onboarding Process** (See: [`video-tab-onboarding-process.md`](./video-tab-onboarding-process.md))
   - Student agreement acceptance
   - Classroom rules acknowledgment
   - Identity validation photo upload
   - ID card validation
   - Session creation and validation

2. **Lesson Player System**
   - Interactive video player with pause controls
   - Maximum 3 pauses per lesson
   - 54-minute total pause time limit
   - Progress tracking and completion detection

3. **Progress Integration**
   - Update `SelfStudyLessons` table (existing) for lesson completion tracking
   - Track `seconds_viewed`, `completed_at`, and progress metrics
   - Sync with existing StudentSidebar component
   - Real-time progress indicators

## 🗃️ Database Structure

### Existing Tables (Verified from Models)
- **`course_auths`**: Student enrollment/purchase records
- **`self_study_lessons`**: Individual lesson progress tracking for offline study
- **`student_unit`**: Classroom day progress (instructor-led with CourseDate)
- **`lessons`**: Master lesson content table
- **`course_units`**: Course unit structure (templates)
- **`course_unit_lessons`**: Lesson ordering within units
- **`validations`**: Existing identity validation system

### New Tables Required for SelfStudy System

#### `self_study_sessions` Table
```sql
CREATE TABLE self_study_sessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    course_auth_id BIGINT UNSIGNED NOT NULL,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    
    -- Onboarding completion tracking
    agreement_completed_at TIMESTAMP NULL,
    rules_completed_at TIMESTAMP NULL,
    identity_validated_at TIMESTAMP NULL,
    id_card_validated_at TIMESTAMP NULL,
    onboarding_completed_at TIMESTAMP NULL,
    
    -- Session management
    expires_at TIMESTAMP NOT NULL,
    last_activity_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (course_auth_id) REFERENCES course_auths(id),
    
    INDEX idx_user_id (user_id),
    INDEX idx_course_auth_id (course_auth_id),
    INDEX idx_session_token (session_token),
    INDEX idx_expires_at (expires_at)
);
```

#### `self_study_video_time_tracking` Table
```sql
CREATE TABLE self_study_video_time_tracking (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_auth_id BIGINT UNSIGNED NOT NULL,
    
    -- Video time allocation & usage
    total_allocated_seconds INT UNSIGNED NOT NULL, -- Config setting (default 36000 = 10 hours)
    total_used_seconds INT UNSIGNED DEFAULT 0,
    remaining_seconds INT UNSIGNED NOT NULL, -- Calculated field: allocated - used
    
    -- Time restoration tracking
    classroom_completed_at TIMESTAMP NULL, -- When student passes classroom version
    time_restored_seconds INT UNSIGNED DEFAULT 0, -- Amount restored after classroom completion
    
    -- Audit trail
    last_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (course_auth_id) REFERENCES course_auths(id),
    UNIQUE KEY unique_course_auth (course_auth_id),
    INDEX idx_remaining_seconds (remaining_seconds)
);
```

#### `self_study_lesson_sessions` Table
```sql
CREATE TABLE self_study_lesson_sessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    self_study_session_id BIGINT UNSIGNED NOT NULL,
    lesson_id SMALLINT UNSIGNED NOT NULL,
    
    -- Lesson player controls
    pause_count TINYINT UNSIGNED DEFAULT 0,
    pause_time_used INT UNSIGNED DEFAULT 0, -- seconds (max 3240 = 54 minutes)
    current_position INT UNSIGNED DEFAULT 0, -- seconds
    
    -- Video time consumption tracking
    video_time_consumed INT UNSIGNED DEFAULT 0, -- seconds of actual video watched
    session_start_time TIMESTAMP NULL,
    session_end_time TIMESTAMP NULL,
    
    -- Progress tracking
    started_at TIMESTAMP NULL,
    last_paused_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (self_study_session_id) REFERENCES self_study_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id),
    
    UNIQUE KEY unique_session_lesson (self_study_session_id, lesson_id),
    INDEX idx_lesson_id (lesson_id),
    INDEX idx_started_at (started_at),
    INDEX idx_video_time_consumed (video_time_consumed)
);
```

### Existing Tables (Integration Points)
- **`validations`**: Will be extended for photo/ID validation in self-study context
- **`self_study_lessons`**: Existing progress tracking (`seconds_viewed`, `completed_at`)
- **`course_auths`**: Student enrollment records (`agreed_at` for course agreement)
- **`lessons`**: Master lesson content

### Data Flow Architecture
```
Student Clicks "View Lesson" (Offline Mode)
    ↓
Check Valid SelfStudySession (24-hour validity + onboarding_completed_at)
    ↓
If No Valid Session → Start Onboarding Process
    ↓ (Create SelfStudySession record)
    1. Agreement → agreement_completed_at timestamp
    2. Rules → rules_completed_at timestamp  
    3. Photo → identity_validated_at timestamp
    4. ID Card → id_card_validated_at timestamp
    5. Complete → onboarding_completed_at timestamp
    ↓
Load Lesson Player → Create SelfStudyLessonSession record
    ↓
Track: pause_count (max 3), pause_time_used (max 54min), current_position
    ↓
Completion → Update SelfStudyLessons & Sync StudentSidebar
```

## 🏗️ Implementation Phases

### Phase 1: Database Schema & Models (Week 1)
- [ ] **Create Migrations**: `self_study_sessions`, `self_study_lesson_sessions`, and `self_study_video_time_tracking` tables
- [ ] **Model Creation**: `SelfStudySession`, `SelfStudyLessonSession`, and `SelfStudyVideoTimeTracking` Eloquent models
- [ ] **Video Time Management**: Implement 10-hour (36,000 seconds) allocation system with restoration logic
- [ ] **Relationships**: Define model relationships with existing `CourseAuth`, `User`, `Lesson` models
- [ ] **Database Analysis**: Document integration with existing `self_study_lessons`, `validations` tables
- [ ] **Time Tracking Logic**: Implement video time consumption and balance management
- [ ] **Basic Onboarding Flow**: Core component structure for session management

### Phase 2: Onboarding System Implementation (Week 2)
- [ ] **Student Agreement Component**: Scrollable agreement with validation
- [ ] **Classroom Rules Component**: Rules acknowledgment system
- [ ] **Identity Validation**: Live photo capture and face detection
- [ ] **ID Card Validation**: OCR and name matching system
- [ ] **Session Management**: Secure session creation and tracking

### Phase 3: Lesson Player & Controls (Week 3)
- [ ] **Video Player Component**: React-based player with controls
- [ ] **Pause Management**: 3-pause limit with 54-minute timer
- [ ] **Video Time Tracking**: Real-time deduction from student's 10-hour allocation
- [ ] **Time Balance Display**: Show remaining video time to student
- [ ] **Time Exhaustion Handling**: Block video access when time runs out
- [ ] **Progress Tracking**: Real-time lesson progress updates
- [ ] **Completion Detection**: Automatic lesson completion marking
- [ ] **Resume Functionality**: Continue from last position

### Phase 4: Integration & Testing (Week 4)
- [ ] **StudentSidebar Integration**: Show self-study progress
- [ ] **Video Tab Updates**: Complete offline mode interface
- [ ] **Mobile Responsiveness**: Optimize for all devices
- [ ] **Security Testing**: Validate all security measures
- [ ] **User Acceptance Testing**: End-to-end flow validation

## 🔧 Technical Components

### Frontend Architecture
```
resources/js/React/Student/Components/
├── VideoTab/
│   ├── VideoTabContainer.tsx          # Main video tab component
│   ├── LessonSelector.tsx             # Lesson selection interface
│   └── OfflineModeButton.tsx          # "Start Lesson" trigger
├── Onboarding/
│   ├── OnboardingFlow.tsx             # Main onboarding container
│   ├── StudentAgreement.tsx           # Agreement acceptance
│   ├── ClassroomRules.tsx             # Rules acknowledgment
│   ├── IdentityValidation.tsx         # Photo capture & validation
│   ├── IDCardValidation.tsx           # ID verification
│   └── OnboardingProgress.tsx         # Progress indicator
├── LessonPlayer/
│   ├── SelfStudyPlayer.tsx            # Main video player
│   ├── PauseManager.tsx               # Pause control system
│   ├── ProgressTracker.tsx            # Progress monitoring
│   └── CompletionHandler.tsx          # Lesson completion
└── Integration/
    └── SelfStudySync.tsx              # StudentSidebar integration
```

### Backend Services
```php
app/Models/
├── SelfStudySession.php               # Session management model
├── SelfStudyLessonSession.php         # Lesson session tracking model
└── SelfStudyVideoTimeTracking.php     # Video time allocation model

app/Services/
├── SelfStudyService.php               # Core self-study logic
├── SelfStudySessionService.php        # Session creation & validation
├── SelfStudyVideoTimeService.php      # Video time allocation & tracking
├── OnboardingService.php              # Onboarding flow management
├── IdentityValidationService.php      # Photo & ID validation (extends existing)
├── LessonPlayerService.php            # Video player backend
└── ProgressTrackingService.php       # Progress management

app/Http/Controllers/
├── SelfStudyController.php            # Main API endpoints
├── SelfStudySessionController.php     # Session management APIs
├── OnboardingController.php           # Onboarding endpoints
├── ValidationController.php           # Photo/ID validation APIs
└── LessonProgressController.php       # Progress tracking APIs
```

## 📱 User Experience Flow

### Lesson Access Trigger
1. Student clicks "View Lesson" button in StudentSidebar (offline mode)
2. System checks for valid onboarding session (24-hour validity)
3. If valid session exists → Direct to lesson player
4. If no valid session → Start onboarding process

### Onboarding Experience
1. **Welcome Screen**: "Complete verification to access your lesson"
2. **Progress Indicator**: Visual step counter (1 of 5, 2 of 5, etc.)
3. **Step-by-Step Validation**: Cannot skip steps, must complete in order
4. **Clear Instructions**: Detailed guidance for each validation step
5. **Error Handling**: Helpful messages for validation failures
6. **Success Confirmation**: "Verification complete! Starting your lesson..."

### Lesson Player Experience
1. **Lesson Introduction**: Brief overview before video starts
2. **Video Controls**: Play, pause, volume, fullscreen
3. **Pause Tracking**: Visual indicator of remaining pauses (3/3, 2/3, etc.)
4. **Timer Display**: Pause time remaining (54:00, 53:45, etc.)
5. **Progress Bar**: Visual lesson completion percentage
6. **Completion Celebration**: Success message and progress update

## ✅ Success Criteria

### Functional Requirements
- [ ] Complete onboarding process with all validation steps
- [ ] Secure photo and ID validation with fraud prevention
- [ ] Video player with enforced pause limits (3 max, 54min total)
- [ ] Accurate progress tracking in SelfStudyLessons table
- [ ] Real-time StudentSidebar progress updates
- [ ] Session management with 24-hour validity

### Performance Requirements
- [ ] Onboarding completion in under 7 minutes
- [ ] Video loading within 3 seconds
- [ ] Photo validation processing under 5 seconds
- [ ] Mobile-responsive on all devices
- [ ] Offline capability where applicable

### Security Requirements
- [ ] Encrypted photo and ID storage
- [ ] Session hijacking prevention
- [ ] Fraud detection and prevention
- [ ] Audit trail for all validations
- [ ] GDPR/CCPA compliance

## 🔒 Security & Compliance

### Data Protection
- All validation photos encrypted at rest
- Automatic data deletion after retention period
- User consent tracking and management
- Audit logs for all access and modifications

### Fraud Prevention
- Live photo capture required (no file uploads)
- Face detection and quality validation
- ID card OCR with name matching
- Session token validation and expiration
- IP and device tracking for suspicious activity

## 📊 Success Metrics & Monitoring

### Completion Rates
- **Target**: >95% successful onboarding completion
- **Measure**: Average completion time per step
- **Monitor**: Common failure points for UX improvement

### Security Effectiveness
- **Track**: Failed validation attempts and patterns
- **Monitor**: Identity verification accuracy rates
- **Measure**: Fraud prevention effectiveness

### User Experience
- **Collect**: User feedback on process difficulty
- **Monitor**: Support requests related to onboarding
- **Track**: Mobile vs desktop success rates

## 📋 Dependencies & Prerequisites

### Existing Systems
- ✅ **StudentSidebar Component**: Recently enhanced with dynamic lessons
- ✅ **Authentication System**: User validation framework
- ✅ **Course/Lesson Models**: Existing data relationships
- ✅ **File Upload Infrastructure**: For photo handling

### Required Integrations
- **Face Detection API**: For identity validation
- **OCR Service**: For ID card text extraction
- **Video Streaming**: For lesson content delivery
- **Mobile Camera Access**: For photo capture

## 🚀 Implementation Timeline

### Week 1: Foundation & Database
- Database schema design and migrations
- Basic onboarding component structure
- Service layer foundation

### Week 2: Onboarding System
- Complete validation flow implementation
- Photo and ID verification systems  
- Session management and security

### Week 3: Lesson Player
- Video player with pause controls
- Progress tracking integration
- StudentSidebar synchronization

### Week 4: Testing & Polish
- End-to-end testing and validation
- Mobile responsiveness optimization
- Security auditing and user acceptance

## 📝 Related Documentation

- [`video-tab-onboarding-process.md`](./video-tab-onboarding-process.md) - Detailed onboarding specification
- [`student-sidebar-dynamic-lessons.md`](./student-sidebar-dynamic-lessons.md) - StudentSidebar integration
- [`../completed-projects/STUDENT_SIDEBAR_COURSE_FILTERING.md`](../completed-projects/STUDENT_SIDEBAR_COURSE_FILTERING.md) - Recent sidebar enhancements

## 🎯 Ready to Begin

This comprehensive task builds upon the recently enhanced StudentSidebar component and provides a complete self-study experience with robust security and validation measures.

**Next Action**: Begin Phase 1 with database analysis and schema design! 🚀

---
**Estimated Completion**: 4 weeks  
**Priority**: High (Core self-study functionality)  
**Team Size**: 2-3 developers  
**Skills Required**: React/TypeScript, PHP/Laravel, Database Design, Security Implementation
