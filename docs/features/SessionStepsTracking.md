# Session Steps Tracking Integration

> **Integrating Student Tracking with Session Step Configuration**

## 🎯 Overview

This document outlines how to integrate the Student Tracking System with the session steps configuration to automatically track student progress through each phase of their learning journey.

---

## 📋 Session Steps Configuration

```typescript
// resources/js/React/Student/config/sessionStepsConfig.ts
import { OnlineStepType, OfflineStepType } from "./types";

/**
 * Configuration object for session steps based on type.
 */
export const sessionStepsConfig: {
    live: OnlineStepType;
    offline: OfflineStepType;
} = {
    live: {
        begin_day: "Begin Day",
        student_agreement: "Student Agreement",
        student_rules: "Student Rules", 
        student_verification: "Student Verification",
        active_classroom: "Active Classroom",
        completed_classroom: "Completed Classroom",
    },

    offline: {
        view_lessons: "View Lessons",
        begin_session: "Begin Session",
        student_agreement: "Student Agreement",
        student_rules: "Student Rules",
        student_verification: "Student Verification", 
        active_session: "Active Session",
        completed_session: "Completed Session",
    },
};
```

## 🔄 Activity Type Mapping

### **Online Session Steps → Activity Types**

```typescript
const onlineActivityMapping = {
    begin_day: 'online_session_start',
    student_agreement: 'online_agreement_accepted',
    student_rules: 'online_rules_acknowledged',
    student_verification: 'online_identity_verified',
    active_classroom: 'online_classroom_entered',
    completed_classroom: 'online_session_end'
};
```

### **Offline Session Steps → Activity Types**

```typescript
const offlineActivityMapping = {
    view_lessons: 'offline_lessons_accessed',
    begin_session: 'offline_session_start',
    student_agreement: 'offline_agreement_accepted',
    student_rules: 'offline_rules_acknowledged', 
    student_verification: 'offline_identity_verified',
    active_session: 'offline_study_active',
    completed_session: 'offline_session_end'
};
```

---

## 🏗️ TypeScript Integration

### **Session Step Types**

```typescript
// resources/js/React/Student/types/sessionSteps.ts

export interface OnlineStepType {
    begin_day: string;
    student_agreement: string;
    student_rules: string;
    student_verification: string;
    active_classroom: string;
    completed_classroom: string;
}

export interface OfflineStepType {
    view_lessons: string;
    begin_session: string;
    student_agreement: string;
    student_rules: string;
    student_verification: string;
    active_session: string;
    completed_session: string;
}

export type SessionType = 'live' | 'offline';
export type OnlineStepKey = keyof OnlineStepType;
export type OfflineStepKey = keyof OfflineStepType;
export type SessionStepKey = OnlineStepKey | OfflineStepKey;

export interface SessionStepEvent {
    stepKey: SessionStepKey;
    sessionType: SessionType;
    timestamp: string;
    courseAuthId: number;
    courseDateId?: number;
    studentUnitId?: number;
    additionalData?: Record<string, any>;
}
```

### **Activity Tracking Hook**

```typescript
// resources/js/React/Student/hooks/useSessionStepTracking.ts

import { useCallback } from 'react';
import { SessionStepEvent, SessionType, SessionStepKey } from '../types/sessionSteps';

export const useSessionStepTracking = () => {
    
    const trackSessionStep = useCallback(async (
        stepKey: SessionStepKey,
        sessionType: SessionType,
        courseAuthId: number,
        additionalData?: Record<string, any>
    ) => {
        try {
            const response = await fetch('/api/student/track-session-step', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    step_key: stepKey,
                    session_type: sessionType,
                    course_auth_id: courseAuthId,
                    additional_data: additionalData,
                    timestamp: new Date().toISOString(),
                }),
            });

            if (!response.ok) {
                console.error('Failed to track session step:', stepKey);
            }
            
            return await response.json();
        } catch (error) {
            console.error('Session step tracking error:', error);
        }
    }, []);

    return { trackSessionStep };
};
```

---

## 🎯 Backend Integration

### **Activity Type Constants**

```php
<?php
// app/Constants/StudentActivityTypes.php

namespace App\Constants;

class StudentActivityTypes
{
    // Online Session Steps
    const ONLINE_SESSION_START = 'online_session_start';
    const ONLINE_AGREEMENT_ACCEPTED = 'online_agreement_accepted';
    const ONLINE_RULES_ACKNOWLEDGED = 'online_rules_acknowledged';
    const ONLINE_IDENTITY_VERIFIED = 'online_identity_verified';
    const ONLINE_CLASSROOM_ENTERED = 'online_classroom_entered';
    const ONLINE_SESSION_END = 'online_session_end';
    
    // Offline Session Steps
    const OFFLINE_LESSONS_ACCESSED = 'offline_lessons_accessed';
    const OFFLINE_SESSION_START = 'offline_session_start'; 
    const OFFLINE_AGREEMENT_ACCEPTED = 'offline_agreement_accepted';
    const OFFLINE_RULES_ACKNOWLEDGED = 'offline_rules_acknowledged';
    const OFFLINE_IDENTITY_VERIFIED = 'offline_identity_verified';
    const OFFLINE_STUDY_ACTIVE = 'offline_study_active';
    const OFFLINE_SESSION_END = 'offline_session_end';
    
    // Step Key to Activity Type Mapping
    public static function getActivityType(string $stepKey, string $sessionType): string
    {
        $mapping = [
            'live' => [
                'begin_day' => self::ONLINE_SESSION_START,
                'student_agreement' => self::ONLINE_AGREEMENT_ACCEPTED,
                'student_rules' => self::ONLINE_RULES_ACKNOWLEDGED,
                'student_verification' => self::ONLINE_IDENTITY_VERIFIED,
                'active_classroom' => self::ONLINE_CLASSROOM_ENTERED,
                'completed_classroom' => self::ONLINE_SESSION_END,
            ],
            'offline' => [
                'view_lessons' => self::OFFLINE_LESSONS_ACCESSED,
                'begin_session' => self::OFFLINE_SESSION_START,
                'student_agreement' => self::OFFLINE_AGREEMENT_ACCEPTED,
                'student_rules' => self::OFFLINE_RULES_ACKNOWLEDGED,
                'student_verification' => self::OFFLINE_IDENTITY_VERIFIED,
                'active_session' => self::OFFLINE_STUDY_ACTIVE,
                'completed_session' => self::OFFLINE_SESSION_END,
            ],
        ];
        
        return $mapping[$sessionType][$stepKey] ?? 'unknown_step';
    }
}
```

### **Session Step Tracking Controller**

```php
<?php
// app/Http/Controllers/Student/SessionStepTrackingController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Classes\StudentDataLayer;
use App\Constants\StudentActivityTypes;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SessionStepTrackingController extends Controller
{
    public function trackSessionStep(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'step_key' => 'required|string',
                'session_type' => 'required|string|in:live,offline',
                'course_auth_id' => 'required|integer',
                'course_date_id' => 'sometimes|integer',
                'student_unit_id' => 'sometimes|integer',
                'additional_data' => 'sometimes|array',
                'timestamp' => 'sometimes|string',
            ]);

            $studentDataLayer = new StudentDataLayer(auth()->id(), $validated['course_auth_id']);
            
            // Get activity type from step key and session type
            $activityType = StudentActivityTypes::getActivityType(
                $validated['step_key'],
                $validated['session_type']
            );
            
            // Determine category
            $category = $validated['session_type'] === 'live' ? 'online' : 'offline';
            
            // Prepare tracking data
            $trackingData = array_merge([
                'step_key' => $validated['step_key'],
                'session_type' => $validated['session_type'],
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
            ], $validated['additional_data'] ?? []);
            
            // Handle session management for start/end steps
            if (in_array($validated['step_key'], ['begin_day', 'begin_session'])) {
                $session = $studentDataLayer->startSession(
                    $validated['session_type'] === 'live' ? 'online' : 'offline',
                    $validated['course_date_id'] ?? null
                );
                $trackingData['session_id'] = $session->id;
            }
            
            // Log the activity
            $activity = $studentDataLayer->logActivity(
                $activityType,
                $category,
                $trackingData,
                null, // lesson_id - will be set for lesson-specific activities
                $validated['course_date_id'] ?? null,
                $validated['student_unit_id'] ?? null
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Session step tracked successfully',
                'data' => [
                    'activity_id' => $activity->id,
                    'activity_type' => $activityType,
                    'step_key' => $validated['step_key'],
                    'session_type' => $validated['session_type'],
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to track session step',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get session step progress for a course
     */
    public function getSessionProgress(Request $request, int $courseAuthId): JsonResponse
    {
        try {
            $studentDataLayer = new StudentDataLayer(auth()->id(), $courseAuthId);
            
            // Get recent session step activities
            $activities = $studentDataLayer->getActivityReport([
                'date_from' => now()->subDays(7)->toDateString(),
                'category' => $request->get('session_type') === 'live' ? 'online' : 'offline'
            ]);
            
            // Group by step key
            $stepProgress = [];
            foreach ($activities['activities'] as $activity) {
                if (isset($activity['data']['step_key'])) {
                    $stepKey = $activity['data']['step_key'];
                    if (!isset($stepProgress[$stepKey])) {
                        $stepProgress[$stepKey] = [];
                    }
                    $stepProgress[$stepKey][] = [
                        'timestamp' => $activity['timestamp'],
                        'session_type' => $activity['data']['session_type'] ?? 'unknown',
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'step_progress' => $stepProgress,
                    'total_activities' => $activities['total_activities'],
                    'by_category' => $activities['by_category'],
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get session progress',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

---

## 🛣️ Route Integration

### **Web Routes**

```php
// routes/web.php - Add to student routes group

Route::middleware(['auth'])->prefix('student')->group(function () {
    // Session Step Tracking
    Route::post('track-session-step', [SessionStepTrackingController::class, 'trackSessionStep'])
         ->name('student.track-session-step');
         
    Route::get('session-progress/{courseAuthId}', [SessionStepTrackingController::class, 'getSessionProgress'])
         ->name('student.session-progress');
});
```

---

## 🎮 Frontend Usage Examples

### **In Session Components**

```typescript
// resources/js/React/Student/Components/SessionManager.tsx

import React, { useEffect } from 'react';
import { useSessionStepTracking } from '../hooks/useSessionStepTracking';
import { sessionStepsConfig } from '../config/sessionStepsConfig';

interface SessionManagerProps {
    sessionType: 'live' | 'offline';
    courseAuthId: number;
    currentStep: string;
}

export const SessionManager: React.FC<SessionManagerProps> = ({
    sessionType,
    courseAuthId,
    currentStep
}) => {
    const { trackSessionStep } = useSessionStepTracking();
    
    // Track step when it changes
    useEffect(() => {
        if (currentStep && courseAuthId) {
            trackSessionStep(
                currentStep as any,
                sessionType,
                courseAuthId,
                {
                    step_label: sessionStepsConfig[sessionType][currentStep as keyof typeof sessionStepsConfig[typeof sessionType]],
                    user_agent: navigator.userAgent,
                    screen_resolution: `${screen.width}x${screen.height}`,
                }
            );
        }
    }, [currentStep, sessionType, courseAuthId, trackSessionStep]);
    
    return (
        <div className="session-manager">
            <h3>Current Step: {sessionStepsConfig[sessionType][currentStep as keyof typeof sessionStepsConfig[typeof sessionType]]}</h3>
            {/* Session content */}
        </div>
    );
};
```

### **Step Progression Component**

```typescript
// resources/js/React/Student/Components/StepProgression.tsx

import React from 'react';
import { sessionStepsConfig } from '../config/sessionStepsConfig';
import { useSessionStepTracking } from '../hooks/useSessionStepTracking';

interface StepProgressionProps {
    sessionType: 'live' | 'offline';
    courseAuthId: number;
    currentStep: string;
    onStepComplete: (step: string) => void;
}

export const StepProgression: React.FC<StepProgressionProps> = ({
    sessionType,
    courseAuthId,
    currentStep,
    onStepComplete
}) => {
    const { trackSessionStep } = useSessionStepTracking();
    const steps = sessionStepsConfig[sessionType];
    
    const handleStepClick = async (stepKey: string) => {
        // Track the step
        await trackSessionStep(
            stepKey as any,
            sessionType,
            courseAuthId,
            {
                triggered_by: 'user_click',
                previous_step: currentStep,
            }
        );
        
        // Update UI
        onStepComplete(stepKey);
    };
    
    return (
        <div className="step-progression">
            {Object.entries(steps).map(([key, label]) => (
                <div
                    key={key}
                    className={`step ${currentStep === key ? 'active' : ''}`}
                    onClick={() => handleStepClick(key)}
                >
                    <span className="step-label">{label}</span>
                </div>
            ))}
        </div>
    );
};
```

---

## 📊 Enhanced Database Schema

### **Updated Activity Types**

Add the new activity types to your existing `student_activities` table by updating the enum or validation rules:

```sql
-- Migration to add session step activity types
ALTER TABLE student_activities 
ADD CONSTRAINT check_session_step_activities 
CHECK (
    activity_type IN (
        -- Existing types...
        'online_session_start', 'online_agreement_accepted', 'online_rules_acknowledged',
        'online_identity_verified', 'online_classroom_entered', 'online_session_end',
        'offline_lessons_accessed', 'offline_session_start', 'offline_agreement_accepted',
        'offline_rules_acknowledged', 'offline_identity_verified', 'offline_study_active',
        'offline_session_end'
    )
);
```

### **Session Step Progress Table** (Optional)

```sql
-- Additional table for session step completion tracking
CREATE TABLE student_session_steps (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    course_auth_id BIGINT NOT NULL,
    course_date_id BIGINT NULL,
    session_type VARCHAR(20) NOT NULL, -- 'live' or 'offline'
    step_key VARCHAR(50) NOT NULL,
    completed_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    duration_seconds INTEGER NULL,
    metadata JSON NULL,
    
    INDEX idx_user_course (user_id, course_auth_id),
    INDEX idx_session_step (session_type, step_key),
    INDEX idx_completed (completed_at),
    
    UNIQUE KEY unique_user_session_step (user_id, course_auth_id, course_date_id, step_key)
);
```

---

## 🔍 Analytics & Reporting

### **Session Step Analytics**

```php
// Add to StudentActivityTrackingTrait

public function getSessionStepAnalytics(array $filters = []): array
{
    $query = StudentActivity::where('user_id', $this->userId)
                           ->where('course_auth_id', $this->courseAuthId)
                           ->whereIn('activity_type', [
                               'online_session_start', 'online_agreement_accepted',
                               'online_rules_acknowledged', 'online_identity_verified',
                               'online_classroom_entered', 'online_session_end',
                               'offline_lessons_accessed', 'offline_session_start',
                               'offline_agreement_accepted', 'offline_rules_acknowledged',
                               'offline_identity_verified', 'offline_study_active',
                               'offline_session_end'
                           ]);
    
    if (isset($filters['session_type'])) {
        $prefix = $filters['session_type'] === 'live' ? 'online_' : 'offline_';
        $query->where('activity_type', 'like', $prefix . '%');
    }
    
    $activities = $query->orderBy('created_at')->get();
    
    return [
        'total_steps_completed' => $activities->count(),
        'steps_by_type' => $activities->groupBy('activity_type')->map->count(),
        'session_completion_rate' => $this->calculateSessionCompletionRate($activities),
        'average_step_duration' => $this->calculateAverageStepDuration($activities),
        'step_timeline' => $activities->map(function($activity) {
            return [
                'step' => $activity->data['step_key'] ?? 'unknown',
                'activity_type' => $activity->activity_type,
                'timestamp' => $activity->created_at->toISOString(),
                'session_type' => $activity->data['session_type'] ?? 'unknown',
            ];
        })->toArray(),
    ];
}
```

---

## 🚀 Implementation Steps

1. **Create Types & Constants**
   - Add TypeScript types for session steps
   - Create PHP constants for activity types

2. **Add Tracking Hook**
   - Implement `useSessionStepTracking` hook
   - Add session step configuration

3. **Backend Integration**
   - Create `SessionStepTrackingController`
   - Add routes for tracking endpoints
   - Update activity type constants

4. **Frontend Components**
   - Update session components to use tracking
   - Add step progression tracking
   - Implement automatic step tracking

5. **Database Updates**
   - Add new activity types to validation
   - Optionally create session steps table
   - Update indexes for performance

6. **Testing & Validation**
   - Test each session step tracking
   - Verify analytics reporting
   - Validate data integrity

---

## 📝 Usage Notes

- **Automatic Tracking**: Steps are tracked automatically when components mount or step changes occur
- **Manual Tracking**: Can also be triggered manually via button clicks or API calls  
- **Session Management**: Start/end steps automatically manage session state
- **Data Integrity**: Unique constraints prevent duplicate step tracking
- **Analytics Ready**: Full analytics and reporting capabilities included

---

**Status:** ✅ Ready for Implementation  
**Dependencies:** StudentTracking.md system, session step configuration  
**Next Steps:** Implement types → Add tracking hook → Create controller → Test integration
