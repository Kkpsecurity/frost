<?php

namespace App\Services;

use App\Models\User;
use App\Models\CourseDate;
use App\Models\InstUnit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Classroom Session Service
 *
 * Handles the creation and management of classroom sessions (InstUnit records)
 * when instructors start teaching classes.
 */
class ClassroomSessionService
{
    /**
     * Start a new classroom session by creating an InstUnit record
     *
     * @param int $courseDateId The CourseDate ID to start
     * @param int|null $assistantId Optional assistant ID
     * @return InstUnit|null The created InstUnit or null on failure
     */
    public function startClassroomSession(int $courseDateId, ?int $assistantId = null): ?InstUnit
    {
        try {
            // Check both admin and user guards
            $user = Auth::guard('admin')->user() ?? Auth::user();
            if (!$user) {
                Log::error('ClassroomSessionService: No authenticated user found');
                return null;
            }

            // Check if CourseDate exists
            $courseDate = CourseDate::find($courseDateId);
            if (!$courseDate) {
                Log::error('ClassroomSessionService: CourseDate not found', [
                    'course_date_id' => $courseDateId
                ]);
                return null;
            }

            // Check if InstUnit already exists for this CourseDate
            if ($courseDate->InstUnit) {
                Log::info('ClassroomSessionService: InstUnit already exists', [
                    'course_date_id' => $courseDateId,
                    'inst_unit_id' => $courseDate->InstUnit->id,
                    'existing_instructor' => $courseDate->InstUnit->created_by
                ]);
                return $courseDate->InstUnit;
            }

            // Create new InstUnit
            $instUnit = InstUnit::create([
                'course_date_id' => $courseDateId,
                'created_by' => $user->id,
                'created_at' => now(),
                'assistant_id' => $assistantId,
            ]);

            Log::info('ClassroomSessionService: InstUnit created successfully', [
                'course_date_id' => $courseDateId,
                'inst_unit_id' => $instUnit->id,
                'instructor_id' => $user->id,
                'instructor_name' => $user->name ?? 'Unknown',
                'assistant_id' => $assistantId
            ]);

            return $instUnit;

        } catch (Exception $e) {
            Log::error('ClassroomSessionService: Error creating InstUnit', [
                'course_date_id' => $courseDateId,
                'assistant_id' => $assistantId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Complete a classroom session
     *
     * @param int $instUnitId The InstUnit ID to complete
     * @return bool Success status
     */
    public function completeClassroomSession(int $instUnitId): bool
    {
        try {
            // Check both admin and user guards
            $user = Auth::guard('admin')->user() ?? Auth::user();
            if (!$user) {
                Log::error('ClassroomSessionService: No authenticated user found for completion');
                return false;
            }

            $instUnit = InstUnit::find($instUnitId);
            if (!$instUnit) {
                Log::error('ClassroomSessionService: InstUnit not found for completion', [
                    'inst_unit_id' => $instUnitId
                ]);
                return false;
            }

            // Update InstUnit with completion info
            $instUnit->update([
                'completed_by' => $user->id,
                'completed_at' => now(),
            ]);

            Log::info('ClassroomSessionService: InstUnit completed successfully', [
                'inst_unit_id' => $instUnitId,
                'completed_by' => $user->id,
                'instructor_name' => $user->name ?? 'Unknown'
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('ClassroomSessionService: Error completing InstUnit', [
                'inst_unit_id' => $instUnitId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Add or update assistant for a classroom session
     *
     * @param int $instUnitId The InstUnit ID
     * @param int $assistantId The assistant User ID
     * @return bool Success status
     */
    public function assignAssistant(int $instUnitId, int $assistantId): bool
    {
        try {
            $instUnit = InstUnit::find($instUnitId);
            if (!$instUnit) {
                Log::error('ClassroomSessionService: InstUnit not found for assistant assignment', [
                    'inst_unit_id' => $instUnitId
                ]);
                return false;
            }

            $assistant = User::find($assistantId);
            if (!$assistant) {
                Log::error('ClassroomSessionService: Assistant user not found', [
                    'assistant_id' => $assistantId
                ]);
                return false;
            }

            $instUnit->update([
                'assistant_id' => $assistantId
            ]);

            Log::info('ClassroomSessionService: Assistant assigned successfully', [
                'inst_unit_id' => $instUnitId,
                'assistant_id' => $assistantId,
                'assistant_name' => $assistant->name ?? 'Unknown'
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('ClassroomSessionService: Error assigning assistant', [
                'inst_unit_id' => $instUnitId,
                'assistant_id' => $assistantId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get classroom session info
     *
     * @param int $courseDateId The CourseDate ID
     * @return array Session information
     */
    public function getClassroomSession(int $courseDateId): array
    {
        try {
            $courseDate = CourseDate::with(['InstUnit.CreatedBy', 'InstUnit.Assistant'])
                ->find($courseDateId);

            if (!$courseDate) {
                return [
                    'exists' => false,
                    'course_date_id' => $courseDateId,
                    'error' => 'CourseDate not found'
                ];
            }

            if (!$courseDate->InstUnit) {
                return [
                    'exists' => false,
                    'course_date_id' => $courseDateId,
                    'course_name' => $courseDate->GetCourse()->title ?? 'Unknown Course'
                ];
            }

            $instUnit = $courseDate->InstUnit;

            return [
                'exists' => true,
                'course_date_id' => $courseDateId,
                'inst_unit_id' => $instUnit->id,
                'course_name' => $courseDate->GetCourse()->title ?? 'Unknown Course',
                'instructor' => [
                    'id' => $instUnit->created_by,
                    'name' => $instUnit->GetCreatedBy()->name ?? 'Unknown Instructor'
                ],
                'assistant' => $instUnit->assistant_id ? [
                    'id' => $instUnit->assistant_id,
                    'name' => $instUnit->GetAssistant()->name ?? 'Unknown Assistant'
                ] : null,
                'created_at' => $instUnit->created_at,
                'completed_at' => $instUnit->completed_at,
                'is_active' => !$instUnit->completed_at
            ];

        } catch (Exception $e) {
            Log::error('ClassroomSessionService: Error getting classroom session', [
                'course_date_id' => $courseDateId,
                'error' => $e->getMessage()
            ]);

            return [
                'exists' => false,
                'course_date_id' => $courseDateId,
                'error' => $e->getMessage()
            ];
        }
    }
}
