<?php

namespace App\Services;

use App\Models\StudentUnit;
use App\Models\StudentActivity;

/**
 * Service for managing onboarding step progression and tracking
 */
class OnboardingStepService
{
    /**
     * Get onboarding steps with completion status
     */
    public function getOnboardingSteps(StudentUnit $studentUnit): array
    {
        $activities = $this->getOnboardingActivities($studentUnit);

        return [
            'agreement' => [
                'completed' => isset($activities['agreement_accepted']),
                'completed_at' => $activities['agreement_accepted']->created_at ?? null,
                'title' => 'Student Agreement',
                'description' => 'Accept course terms and expectations',
                'step_number' => 1,
            ],
            'rules' => [
                'completed' => isset($activities['rules_acknowledged']),
                'completed_at' => $activities['rules_acknowledged']->created_at ?? null,
                'title' => 'Classroom Rules',
                'description' => 'Acknowledge classroom conduct and participation rules',
                'step_number' => 2,
            ],
            'identity' => [
                'completed' => isset($activities['identity_verified']),
                'completed_at' => $activities['identity_verified']->created_at ?? null,
                'title' => 'Identity Verification',
                'description' => 'Verify ID and capture daily headshot',
                'step_number' => 3,
            ],
            'entry' => [
                'completed' => isset($activities['onboarding_completed']) || !is_null($studentUnit->completed_at),
                'completed_at' => $activities['onboarding_completed']->created_at ?? $studentUnit->completed_at,
                'title' => 'Enter Classroom',
                'description' => 'Complete setup and join the class',
                'step_number' => 4,
            ],
        ];
    }

    /**
     * Get completion progress as percentage
     */
    public function getCompletionProgress(StudentUnit $studentUnit): array
    {
        $steps = $this->getOnboardingSteps($studentUnit);
        $completedSteps = collect($steps)->where('completed', true)->count();
        $totalSteps = count($steps);
        $percentage = $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0;

        return [
            'completed_steps' => $completedSteps,
            'total_steps' => $totalSteps,
            'percentage' => $percentage,
            'is_complete' => $completedSteps === $totalSteps
        ];
    }

    /**
     * Get next incomplete step
     */
    public function getNextStep(StudentUnit $studentUnit): ?string
    {
        $steps = $this->getOnboardingSteps($studentUnit);

        foreach ($steps as $stepKey => $step) {
            if (!$step['completed']) {
                return $stepKey;
            }
        }

        return null; // All steps complete
    }

    /**
     * Check if student can access a specific step
     */
    public function canAccessStep(StudentUnit $studentUnit, string $stepKey): bool
    {
        $steps = $this->getOnboardingSteps($studentUnit);
        $stepNumbers = collect($steps)->pluck('step_number', '')->flip();

        $currentStepNumber = $stepNumbers[$stepKey] ?? 0;

        // Can access step 1 (agreement) immediately
        if ($currentStepNumber <= 1) {
            return true;
        }

        // For other steps, previous step must be completed
        $previousSteps = collect($steps)->filter(function ($step) use ($currentStepNumber) {
            return $step['step_number'] < $currentStepNumber;
        });

        return $previousSteps->every('completed');
    }

    /**
     * Check if onboarding is fully complete
     */
    public function isOnboardingComplete(StudentUnit $studentUnit): bool
    {
        return $this->getCompletionProgress($studentUnit)['is_complete'];
    }

    /**
     * Get onboarding activities for a student unit
     */
    private function getOnboardingActivities(StudentUnit $studentUnit)
    {
        return StudentActivity::where('student_unit_id', $studentUnit->id)
            ->whereIn('action', [
                'agreement_accepted',
                'rules_acknowledged',
                'identity_verified',
                'onboarding_completed'
            ])
            ->get()
            ->keyBy('action');
    }
}
