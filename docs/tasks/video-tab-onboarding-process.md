# Video Tab Self-Study Onboarding Process

**Date**: September 19, 2025  
**Status**: Planning Phase  
**Priority**: High - Required for self-study access

## 📋 Onboarding Process Overview

The onboarding process ensures student compliance and identity verification before accessing self-study lessons. Students must complete all steps in sequence to unlock lesson access.

## 🔄 Onboarding Flow Sequence

### Step 1: Student Agreement Acceptance
**Required**: Student must read and accept the student agreement
- **Display**: Full student agreement document with scroll tracking
- **Validation**: Must scroll to bottom and check "I agree" checkbox
- **Storage**: Record agreement acceptance with timestamp
- **Database**: `student_agreements` table with `user_id`, `agreement_version`, `accepted_at`

### Step 2: Classroom Rules Online Acknowledgment  
**Required**: Student must acknowledge online classroom rules
- **Display**: Online classroom rules and conduct expectations
- **Validation**: Must check "I understand and agree to follow these rules"
- **Storage**: Record rules acknowledgment with timestamp
- **Database**: `classroom_rules_acknowledgments` table

### Step 3: Identity Validation Photo Upload
**Required**: Live photo capture for identity verification
- **Process**: 
  1. Camera access request
  2. Live photo capture (not file upload)
  3. Photo quality validation (face detection, lighting, clarity)
  4. Photo comparison with enrollment photo (if available)
- **Validation**: Face detection API confirms valid identity photo
- **Storage**: Encrypted photo storage with session linking
- **Database**: `identity_validations` table with photo path and validation status

### Step 4: ID Card Validation
**Required**: Government-issued ID verification
- **Process**:
  1. ID card photo capture or upload
  2. OCR text extraction from ID
  3. Name matching against student record
  4. ID expiration date validation
- **Validation**: Name must match student account, ID must be current
- **Storage**: Encrypted ID photo with extracted data
- **Database**: `id_validations` table with extracted information

### Step 5: Final Verification & Session Creation
**Automatic**: System validates all requirements completed
- **Validation**: All previous steps marked as completed
- **Action**: Create `SelfStudyUnit` session record
- **Result**: Unlock lesson access for current session
- **Expiration**: Session valid for 24 hours or until lesson completion

## 🗃️ Database Schema Requirements

### New Tables Needed

```sql
-- Student Agreement Tracking
CREATE TABLE student_agreements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    agreement_version VARCHAR(50) NOT NULL,
    agreement_content TEXT NOT NULL,
    accepted_at TIMESTAMP NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Classroom Rules Acknowledgment
CREATE TABLE classroom_rules_acknowledgments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    rules_version VARCHAR(50) NOT NULL,
    acknowledged_at TIMESTAMP NOT NULL,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Identity Photo Validation
CREATE TABLE identity_validations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    session_id VARCHAR(255) NOT NULL,
    photo_path VARCHAR(500) NOT NULL,
    validation_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    face_detected BOOLEAN DEFAULT FALSE,
    quality_score DECIMAL(3,2) DEFAULT 0.00,
    validation_notes TEXT,
    validated_at TIMESTAMP NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ID Card Validation
CREATE TABLE id_validations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    session_id VARCHAR(255) NOT NULL,
    id_photo_path VARCHAR(500) NOT NULL,
    extracted_name VARCHAR(255),
    extracted_dob DATE,
    extracted_id_number VARCHAR(100),
    extracted_expiration DATE,
    name_match_score DECIMAL(3,2) DEFAULT 0.00,
    validation_status ENUM('pending', 'approved', rejected') DEFAULT 'pending',
    validation_notes TEXT,
    validated_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Onboarding Session Tracking
CREATE TABLE onboarding_sessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    session_id VARCHAR(255) UNIQUE NOT NULL,
    agreement_completed BOOLEAN DEFAULT FALSE,
    rules_completed BOOLEAN DEFAULT FALSE,
    identity_completed BOOLEAN DEFAULT FALSE,
    id_card_completed BOOLEAN DEFAULT FALSE,
    onboarding_completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMP NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## 🎨 Frontend Components Architecture

### Main Onboarding Component
```typescript
// OnboardingFlow.tsx
interface OnboardingFlowProps {
    userId: number;
    courseAuthId: number;
    onComplete: (sessionId: string) => void;
    onCancel: () => void;
}

const OnboardingFlow: React.FC<OnboardingFlowProps> = ({
    userId,
    courseAuthId,
    onComplete,
    onCancel
}) => {
    const [currentStep, setCurrentStep] = useState(1);
    const [sessionId, setSessionId] = useState<string>('');
    const [completedSteps, setCompletedSteps] = useState<boolean[]>([]);
    
    return (
        <div className="onboarding-container">
            <OnboardingProgress currentStep={currentStep} totalSteps={5} />
            {currentStep === 1 && <StudentAgreement onComplete={handleStep1Complete} />}
            {currentStep === 2 && <ClassroomRules onComplete={handleStep2Complete} />}
            {currentStep === 3 && <IdentityValidation onComplete={handleStep3Complete} />}
            {currentStep === 4 && <IDCardValidation onComplete={handleStep4Complete} />}
            {currentStep === 5 && <FinalVerification onComplete={handleFinalComplete} />}
        </div>
    );
};
```

### Individual Step Components

```typescript
// StudentAgreement.tsx
interface StudentAgreementProps {
    onComplete: (data: AgreementData) => void;
}

// ClassroomRules.tsx
interface ClassroomRulesProps {
    onComplete: (acknowledged: boolean) => void;
}

// IdentityValidation.tsx
interface IdentityValidationProps {
    onComplete: (photoData: PhotoValidationData) => void;
}

// IDCardValidation.tsx
interface IDCardValidationProps {
    onComplete: (idData: IDValidationData) => void;
}
```

## 🔧 Backend Services Implementation

### OnboardingService
```php
<?php

namespace App\Services;

class OnboardingService
{
    public function createSession(int $userId): string
    {
        // Create new onboarding session
        // Return unique session ID
    }
    
    public function validateStep(string $sessionId, int $step, array $data): bool
    {
        // Validate individual onboarding step
        // Update session progress
    }
    
    public function completeOnboarding(string $sessionId): bool
    {
        // Mark onboarding as complete
        // Create SelfStudyUnit session
        // Return success status
    }
    
    public function getSessionStatus(string $sessionId): array
    {
        // Return current onboarding progress
    }
}
```

### IdentityValidationService
```php
<?php

namespace App\Services;

class IdentityValidationService
{
    public function validatePhoto(string $photoPath, int $userId): array
    {
        // Perform face detection
        // Check photo quality
        // Compare with enrollment photo if available
        // Return validation results
    }
    
    public function validateIDCard(string $idPhotoPath, int $userId): array
    {
        // Perform OCR on ID card
        // Extract relevant information
        // Match name against user record
        // Validate expiration date
        // Return validation results
    }
}
```

## 📱 User Experience Flow

### Visual Progress Indicator
- **Progress Bar**: Shows 5 steps with current position
- **Step Icons**: Visual indicators for each onboarding step
- **Completion Status**: Green checkmarks for completed steps
- **Time Estimate**: "Approximately 5-7 minutes to complete"

### Error Handling
- **Photo Quality Issues**: "Please retake photo in better lighting"
- **ID Validation Failures**: "ID name doesn't match account. Please contact support"
- **Technical Errors**: "Connection issue. Your progress has been saved"
- **Timeout Handling**: "Session expired. Please restart onboarding process"

### Mobile Responsiveness
- **Camera Access**: Native mobile camera integration
- **Touch-Friendly**: Large buttons and clear instructions
- **Portrait Orientation**: Optimized for phone use
- **Offline Handling**: Clear messaging when internet is required

## ✅ Validation Rules & Requirements

### Student Agreement
- Must scroll to view entire document
- Cannot skip or fast-scroll
- Must explicitly check agreement checkbox
- Records scroll behavior and time spent reading

### Classroom Rules
- Must view complete rules document
- Must acknowledge understanding
- Cannot proceed without explicit acceptance
- Records timestamp of acknowledgment

### Identity Photo
- Must be live capture (not file upload)
- Face must be clearly visible and centered
- Adequate lighting and image quality required
- No filters or digital modifications allowed

### ID Card Validation
- Government-issued ID required
- ID must not be expired
- Name must match student account (fuzzy matching allowed)
- ID must be clearly readable and unobstructed

## 🔒 Security & Privacy Considerations

### Data Protection
- All photos encrypted at rest
- Photo access logged and monitored
- Automatic photo deletion after validation period
- GDPR/CCPA compliance for data handling

### Session Security
- Unique session tokens with expiration
- IP address and device tracking
- Prevents session sharing or reuse
- Automatic logout on suspicious activity

### Validation Integrity
- Multiple validation checkpoints
- Admin review for failed validations
- Audit trail for all onboarding activities
- Appeals process for validation disputes

## 🎯 Success Metrics

### Completion Rates
- Target: >95% successful onboarding completion
- Measure: Average time per step
- Track: Common failure points for improvement

### Security Effectiveness
- Monitor: Failed validation attempts
- Track: Identity verification accuracy
- Measure: Fraud prevention effectiveness

### User Experience
- Collect: User feedback on process difficulty
- Monitor: Support requests related to onboarding
- Track: Mobile vs desktop completion rates

## 📋 Implementation Timeline

### Week 1: Foundation
- **Days 1-2**: Database schema design and migration
- **Days 3-4**: Basic onboarding flow component structure
- **Day 5**: Student agreement and rules components

### Week 2: Validation Systems
- **Days 1-2**: Photo capture and identity validation
- **Days 3-4**: ID card OCR and validation
- **Day 5**: Session management and progress tracking

### Week 3: Integration & Testing
- **Days 1-2**: Integration with video tab and lesson player
- **Days 3-4**: Mobile responsiveness and error handling
- **Day 5**: Security testing and user acceptance testing

## 🚀 Ready for Implementation

This comprehensive onboarding process ensures:
- **Legal Compliance**: Student agreement and rules acknowledgment
- **Identity Security**: Multi-step validation prevents impersonation
- **User Experience**: Clear, guided process with progress tracking
- **Technical Integration**: Seamless connection to lesson access
- **Security**: Robust validation and session management

**Next Step**: Begin database schema implementation and basic component structure! 🎯
