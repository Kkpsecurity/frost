<?php

namespace App\Services;

use App\Models\User;
use App\Models\Validation;
use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Models\StudentUnit;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Identity Verification Service
 *
 * Centralized service for student identity verification approvals.
 * Used by instructors, assistants, and support team.
 *
 * Supports both:
 * - Individual validation approval (ID card OR headshot separately)
 * - Bulk approval (both together for convenience)
 */
class IdentityVerificationService
{
    /**
     * Approve a single validation (ID card OR headshot)
     *
     * @param int $validationId
     * @param User $approver
     * @param string|null $notes
     * @return array
     */
    public function approveSingleValidation(int $validationId, User $approver, ?string $notes = null): array
    {
        try {
            $validation = Validation::findOrFail($validationId);

            // Determine validation type
            $validationType = $validation->course_auth_id ? 'id_card' : 'headshot';

            // Approve the validation
            if ($validationType === 'id_card') {
                $validation->Accept('id');
            } else {
                $validation->Accept('headshot');
            }

            // Check if both validations are now approved for this student
            $fullyVerified = false;
            $studentUnit = null;

            if ($validation->course_auth_id) {
                // This is ID card - check for headshot approval
                $courseAuth = CourseAuth::find($validation->course_auth_id);
                if ($courseAuth) {
                    $studentUnits = StudentUnit::where('course_auth_id', $courseAuth->id)->get();
                    foreach ($studentUnits as $su) {
                        $headshotValidation = Validation::where('student_unit_id', $su->id)->first();
                        if ($headshotValidation && $headshotValidation->status == 1) {
                            $studentUnit = $su;
                            $fullyVerified = true;
                            break;
                        }
                    }
                }
            } else if ($validation->student_unit_id) {
                // This is headshot - check for ID card approval
                $studentUnit = StudentUnit::find($validation->student_unit_id);
                
                Log::info('Headshot approval - checking for ID card', [
                    'student_unit_id' => $validation->student_unit_id,
                    'student_unit_found' => $studentUnit ? 'yes' : 'no',
                    'course_auth_id' => $studentUnit ? $studentUnit->course_auth_id : null,
                ]);
                
                if ($studentUnit) {
                    $idCardValidation = Validation::where('course_auth_id', $studentUnit->course_auth_id)->first();
                    
                    Log::info('ID card validation lookup', [
                        'id_card_validation_id' => $idCardValidation ? $idCardValidation->id : null,
                        'id_card_status' => $idCardValidation ? $idCardValidation->status : null,
                    ]);
                    
                    if ($idCardValidation && $idCardValidation->status == 1) {
                        $fullyVerified = true;
                        
                        Log::info('Both validations approved - setting fully verified');
                    } else {
                        Log::info('ID card not approved yet', [
                            'id_card_exists' => $idCardValidation ? 'yes' : 'no',
                            'id_card_status' => $idCardValidation ? $idCardValidation->status : null,
                        ]);
                    }
                }
            }

            // Update StudentUnit if both are approved
            if ($fullyVerified && $studentUnit) {
                $studentUnit->verified = true;
                $studentUnit->verification_method = 'manual_' . $this->getApproverRole($approver);
                $studentUnit->save();
            }

            Log::info('Single validation approved', [
                'approver_id' => $approver->id,
                'approver_role' => $this->getApproverRole($approver),
                'validation_id' => $validationId,
                'validation_type' => $validationType,
                'fully_verified' => $fullyVerified,
                'notes' => $notes,
            ]);

            return [
                'success' => true,
                'validation_type' => $validationType,
                'fully_verified' => $fullyVerified,
                'message' => $fullyVerified
                    ? 'Validation approved - Student identity fully verified'
                    : ucfirst($validationType) . ' approved - Awaiting other validation',
            ];

        } catch (Exception $e) {
            Log::error('Failed to approve single validation', [
                'approver_id' => $approver->id,
                'validation_id' => $validationId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to approve validation',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Reject a single validation (ID card OR headshot)
     *
     * @param int $validationId
     * @param User $rejector
     * @param string $reason
     * @param string|null $notes
     * @return array
     */
    public function rejectSingleValidation(int $validationId, User $rejector, string $reason, ?string $notes = null): array
    {
        try {
            $validation = Validation::findOrFail($validationId);

            // Determine validation type
            $validationType = $validation->course_auth_id ? 'id_card' : 'headshot';

            $fullReason = $notes ? "$reason - $notes" : $reason;
            $validation->Reject($fullReason);

            // Update StudentUnit to not verified if either validation is rejected
            $studentUnit = null;
            if ($validation->student_unit_id) {
                $studentUnit = StudentUnit::find($validation->student_unit_id);
            } else if ($validation->course_auth_id) {
                $courseAuth = CourseAuth::find($validation->course_auth_id);
                if ($courseAuth) {
                    $studentUnit = StudentUnit::where('course_auth_id', $courseAuth->id)->first();
                }
            }

            if ($studentUnit) {
                $studentUnit->verified = false;
                $studentUnit->verification_method = null;
                $studentUnit->save();
            }

            Log::info('Single validation rejected', [
                'rejector_id' => $rejector->id,
                'rejector_role' => $this->getApproverRole($rejector),
                'validation_id' => $validationId,
                'validation_type' => $validationType,
                'reason' => $reason,
                'notes' => $notes,
            ]);

            return [
                'success' => true,
                'validation_type' => $validationType,
                'message' => ucfirst($validationType) . ' rejected',
            ];

        } catch (Exception $e) {
            Log::error('Failed to reject single validation', [
                'rejector_id' => $rejector->id,
                'validation_id' => $validationId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to reject validation',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Approve student identity (both ID card and headshot together - BULK)
     *
     * @param int $studentId
     * @param int $courseDateId
     * @param User $approver (instructor/assistant/support)
     * @param string|null $notes
     * @return array
     */
    public function approveIdentity(int $studentId, int $courseDateId, User $approver, ?string $notes = null): array
    {
        try {
            // Get CourseDate and CourseAuth
            $courseDate = CourseDate::with(['CourseUnit'])->findOrFail($courseDateId);
            $courseId = $courseDate->CourseUnit->course_id ?? null;

            if (!$courseId) {
                throw new Exception('Invalid course date - no course ID');
            }

            $courseAuth = CourseAuth::where('user_id', $studentId)
                ->where('course_id', $courseId)
                ->firstOrFail();

            // Get StudentUnit
            $studentUnit = StudentUnit::where('course_auth_id', $courseAuth->id)
                ->where('course_date_id', $courseDateId)
                ->firstOrFail();

            // Get both validations
            $idCardValidation = Validation::where('course_auth_id', $courseAuth->id)->first();
            $headshotValidation = Validation::where('student_unit_id', $studentUnit->id)->first();

            $approvedCount = 0;
            $errors = [];

            // Approve ID card
            if ($idCardValidation) {
                try {
                    $idCardValidation->Accept('id');
                    $approvedCount++;
                } catch (Exception $e) {
                    $errors[] = 'ID card: ' . $e->getMessage();
                }
            } else {
                $errors[] = 'ID card validation not found';
            }

            // Approve headshot
            if ($headshotValidation) {
                try {
                    $headshotValidation->Accept('headshot');
                    $approvedCount++;
                } catch (Exception $e) {
                    $errors[] = 'Headshot: ' . $e->getMessage();
                }
            } else {
                $errors[] = 'Headshot validation not found';
            }

            // Update StudentUnit if both approved
            if ($approvedCount === 2) {
                $studentUnit->verified = true;
                $studentUnit->verification_method = 'manual_' . $this->getApproverRole($approver);
                $studentUnit->save();
            }

            Log::info('Identity verification approved', [
                'approver_id' => $approver->id,
                'approver_role' => $this->getApproverRole($approver),
                'student_id' => $studentId,
                'course_date_id' => $courseDateId,
                'approved_count' => $approvedCount,
                'notes' => $notes,
            ]);

            return [
                'success' => $approvedCount > 0,
                'approved_count' => $approvedCount,
                'total_validations' => 2,
                'fully_verified' => $approvedCount === 2,
                'errors' => $errors,
                'message' => $approvedCount === 2
                    ? 'Student identity fully verified'
                    : 'Partial verification completed',
            ];

        } catch (Exception $e) {
            Log::error('Failed to approve identity', [
                'approver_id' => $approver->id,
                'student_id' => $studentId,
                'course_date_id' => $courseDateId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'approved_count' => 0,
                'total_validations' => 2,
                'fully_verified' => false,
                'errors' => [$e->getMessage()],
                'message' => 'Failed to approve identity',
            ];
        }
    }

    /**
     * Reject student identity (both ID card and headshot)
     *
     * @param int $studentId
     * @param int $courseDateId
     * @param User $rejector
     * @param string $reason
     * @param string|null $notes
     * @return array
     */
    public function rejectIdentity(int $studentId, int $courseDateId, User $rejector, string $reason, ?string $notes = null): array
    {
        try {
            $courseDate = CourseDate::with(['CourseUnit'])->findOrFail($courseDateId);
            $courseId = $courseDate->CourseUnit->course_id ?? null;

            if (!$courseId) {
                throw new Exception('Invalid course date - no course ID');
            }

            $courseAuth = CourseAuth::where('user_id', $studentId)
                ->where('course_id', $courseId)
                ->firstOrFail();

            $studentUnit = StudentUnit::where('course_auth_id', $courseAuth->id)
                ->where('course_date_id', $courseDateId)
                ->firstOrFail();

            // Get both validations
            $idCardValidation = Validation::where('course_auth_id', $courseAuth->id)->first();
            $headshotValidation = Validation::where('student_unit_id', $studentUnit->id)->first();

            $rejectedCount = 0;
            $fullReason = $notes ? "$reason - $notes" : $reason;

            // Reject ID card
            if ($idCardValidation) {
                $idCardValidation->Reject($fullReason);
                $rejectedCount++;
            }

            // Reject headshot
            if ($headshotValidation) {
                $headshotValidation->Reject($fullReason);
                $rejectedCount++;
            }

            // Update StudentUnit
            $studentUnit->verified = false;
            $studentUnit->verification_method = null;
            $studentUnit->save();

            Log::info('Identity verification rejected', [
                'rejector_id' => $rejector->id,
                'rejector_role' => $this->getApproverRole($rejector),
                'student_id' => $studentId,
                'course_date_id' => $courseDateId,
                'rejected_count' => $rejectedCount,
                'reason' => $reason,
                'notes' => $notes,
            ]);

            return [
                'success' => true,
                'rejected_count' => $rejectedCount,
                'total_validations' => 2,
                'message' => 'Identity verification rejected',
            ];

        } catch (Exception $e) {
            Log::error('Failed to reject identity', [
                'rejector_id' => $rejector->id,
                'student_id' => $studentId,
                'course_date_id' => $courseDateId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'rejected_count' => 0,
                'total_validations' => 2,
                'message' => 'Failed to reject identity',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Request new verification photo(s)
     *
     * @param int $studentId
     * @param int $courseDateId
     * @param User $requester
     * @param string $photoType ('id_card', 'headshot', 'both')
     * @param string|null $notes
     * @return array
     */
    public function requestNewPhoto(int $studentId, int $courseDateId, User $requester, string $photoType, ?string $notes = null): array
    {
        try {
            $courseDate = CourseDate::with(['CourseUnit'])->findOrFail($courseDateId);
            $courseId = $courseDate->CourseUnit->course_id ?? null;

            if (!$courseId) {
                throw new Exception('Invalid course date - no course ID');
            }

            $courseAuth = CourseAuth::where('user_id', $studentId)
                ->where('course_id', $courseId)
                ->firstOrFail();

            $studentUnit = StudentUnit::where('course_auth_id', $courseAuth->id)
                ->where('course_date_id', $courseDateId)
                ->first();

            $requestedCount = 0;
            $requestReason = $notes ?: 'Please provide a new verification photo';

            // Request new ID card
            if (in_array($photoType, ['id_card', 'both'])) {
                $idCardValidation = Validation::where('course_auth_id', $courseAuth->id)->first();
                if ($idCardValidation) {
                    $idCardValidation->Reject($requestReason);
                    $requestedCount++;
                }
            }

            // Request new headshot
            if (in_array($photoType, ['headshot', 'both']) && $studentUnit) {
                $headshotValidation = Validation::where('student_unit_id', $studentUnit->id)->first();
                if ($headshotValidation) {
                    $headshotValidation->Reject($requestReason);
                    $requestedCount++;
                }
            }

            // TODO: Send notification to student

            Log::info('New verification photo requested', [
                'requester_id' => $requester->id,
                'requester_role' => $this->getApproverRole($requester),
                'student_id' => $studentId,
                'course_date_id' => $courseDateId,
                'photo_type' => $photoType,
                'requested_count' => $requestedCount,
                'notes' => $notes,
            ]);

            return [
                'success' => true,
                'requested_count' => $requestedCount,
                'photo_type' => $photoType,
                'message' => $requestedCount > 0
                    ? 'New photo request sent to student'
                    : 'No validations found to request',
            ];

        } catch (Exception $e) {
            Log::error('Failed to request new photo', [
                'requester_id' => $requester->id,
                'student_id' => $studentId,
                'course_date_id' => $courseDateId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'requested_count' => 0,
                'photo_type' => $photoType,
                'message' => 'Failed to request new photo',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get approver role for logging
     *
     * @param User $user
     * @return string
     */
    private function getApproverRole(User $user): string
    {
        if ($user->hasRole('support')) {
            return 'support';
        }
        if ($user->hasRole('admin')) {
            return 'admin';
        }
        if ($user->hasRole('instructor')) {
            return 'instructor';
        }
        if ($user->hasRole('assistant')) {
            return 'assistant';
        }
        return 'unknown';
    }
}
