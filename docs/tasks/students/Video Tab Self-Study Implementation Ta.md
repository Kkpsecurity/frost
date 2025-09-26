# Video Tab Self-Study Implementation Task

## 📋 Task Overview
Implement the Video tab functionality for self-study mode where students can access lessons independently with photo validation, lesson player controls, and progress tracking.

## 🎯 Requirements Analysis

### Core Functionality
1. **Access Control**: Validate student identity with photo upload before starting lesson
2. **Lesson Player**: Interactive video player with pause controls and time limits
3. **Progress Tracking**: Track completion status in `SelfStudyUnit` and `SelfStudyLessons`
4. **Onboarding Flow**: Guide students through lesson introduction before content
5. **Pause Management**: Allow 3 pauses maximum with 54-minute total pause time

### Database Structure
- **`StudentUnit`**: Represents classroom day progress (instructor-led)
- **`SelfStudyUnit`**: Represents self-study session progress (offline mode)
- **`SelfStudyLessons`**: Individual lesson completion tracking for self-study

## 🏗️ Implementation Plan

### Phase 1: Database & Models Analysis
- [ ] Examine existing `SelfStudyUnit` and `SelfStudyLessons` tables
- [ ] Verify relationships between models
- [ ] Check current validation and tracking fields
- [ ] Document data flow for self-study sessions

### Phase 2: Photo Validation System
- [ ] Create photo capture/upload interface
- [ ] Implement server-side photo validation
- [ ] Store validation records with session tracking
- [ ] Handle validation failures and retry logic

### Phase 3: Lesson Player Implementation
- [ ] Build React-based video player component
- [ ] Implement pause tracking system (3 pauses max)
- [ ] Add pause timer (54-minute total limit)
- [ ] Create progress indicators and controls

### Phase 4: Onboarding Flow
- [ ] Design lesson introduction screens
- [ ] Implement pre-lesson requirements check
- [ ] Create smooth transition to lesson content
- [ ] Add navigation and progress breadcrumbs

### Phase 5: Progress Tracking Integration
- [ ] Update `SelfStudyLessons` on completion
- [ ] Sync with existing StudentSidebar component
- [ ] Handle lesson state management
- [ ] Implement resume functionality

### Phase 6: UI/UX Integration
- [ ] Update Video tab in student dashboard
- [ ] Integrate with existing lesson sidebar
- [ ] Add completion indicators
- [ ] Mobile responsiveness testing

## 📊 Data Flow Architecture

```
Student Clicks "View Lesson" 
    ↓
Photo Validation Required
    ↓
Create/Update SelfStudyUnit Record
    ↓
Show Onboarding Flow
    ↓
Load Lesson Player
    ↓
Track Progress & Pauses
    ↓
Mark SelfStudyLesson Complete
    ↓
Update StudentSidebar Status
```

## 🔧 Technical Implementation

### Frontend Components Needed
1. **PhotoValidation** - Camera/upload interface
2. **LessonPlayer** - Video player with controls
3. **OnboardingFlow** - Pre-lesson introduction
4. **ProgressTracker** - Real-time progress display
5. **PauseManager** - Pause limit enforcement

### Backend Services Required
1. **SelfStudyService** - Core business logic
2. **PhotoValidationService** - Identity verification
3. **LessonProgressService** - Progress tracking
4. **VideoStreamingService** - Lesson content delivery

## 🎯 Success Criteria

### Functional Requirements
- [ ] Students can validate identity before lessons
- [ ] Lesson player enforces pause limits correctly
- [ ] Progress is accurately tracked in database
- [ ] Completed lessons show in StudentSidebar
- [ ] Smooth user experience throughout flow

### Performance Requirements
- [ ] Video loading < 3 seconds
- [ ] Photo validation < 5 seconds
- [ ] Progress saves every 30 seconds
- [ ] Responsive on mobile devices

## 📝 Implementation Timeline

**Week 1**: Database analysis and photo validation POC
**Week 2**: Lesson player development and progress tracking
**Week 3**: UI polish and system integration

## 🚀 Ready to Begin
This task builds on the existing StudentSidebar component we enhanced and leverages the current course/lesson structure for seamless self-study implementation.