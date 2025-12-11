<?php

namespace App\Services;

/**
 * @file CourseAuthService.php
 * @brief Service for managing CourseAuth creation and lifecycle from orders.
 * @details Handles CourseAuth creation from orders and manual grants with strict separation.
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Models\CourseAuth;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Course;
use App\Services\RCache;

class CourseAuthService
{
    /**
     * Grant CourseAuth from order item (idempotent)
     */
    public function grantFromOrder(int $orderItemId): array
    {
        try {
            DB::beginTransaction();

            $orderItem = OrderItem::with(['order.user', 'course'])->findOrFail($orderItemId);
            
            // Check if CourseAuth already exists for this order item
            $existingAuth = CourseAuth::where('source_type', 'order')
                ->where('source_id', $orderItemId)
                ->first();

            if ($existingAuth) {
                DB::rollBack();
                return [
                    'success' => true,
                    'course_auth' => $existingAuth,
                    'message' => 'CourseAuth already exists for this order item',
                    'duplicate' => true,
                ];
            }

            // Check for existing active CourseAuth for same user/course
            $activeAuth = CourseAuth::where('user_id', $orderItem->order->user_id)
                ->where('course_id', $orderItem->course_id)
                ->where(function ($query) {
                    $query->whereNull('disabled_at')
                        ->whereNull('completed_at')
                        ->where(function ($q) {
                            $q->whereNull('expires_at')
                                ->orWhere('expires_at', '>', Carbon::now());
                        });
                })
                ->first();

            if ($activeAuth) {
                // Log audit event but don't create duplicate
                Log::info('Duplicate CourseAuth prevention', [
                    'user_id' => $orderItem->order->user_id,
                    'course_id' => $orderItem->course_id,
                    'order_item_id' => $orderItemId,
                    'existing_auth_id' => $activeAuth->id,
                    'existing_source_type' => $activeAuth->source_type,
                ]);

                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'User already has active CourseAuth for this course',
                    'existing_auth' => $activeAuth,
                    'duplicate_prevention' => true,
                ];
            }

            // Create new CourseAuth
            $courseAuth = $this->createCourseAuth([
                'user_id' => $orderItem->order->user_id,
                'course_id' => $orderItem->course_id,
                'source_type' => 'order',
                'source_id' => $orderItemId,
                'starts_at' => Carbon::now(),
                'expires_at' => $this->calculateExpiration($orderItem->course),
                'status' => 'active',
            ]);

            // Update order item status
            $orderItem->update(['status' => OrderItem::STATUS_FULFILLED]);

            DB::commit();

            Log::info('CourseAuth granted from order', [
                'course_auth_id' => $courseAuth->id,
                'user_id' => $courseAuth->user_id,
                'course_id' => $courseAuth->course_id,
                'order_item_id' => $orderItemId,
            ]);

            return [
                'success' => true,
                'course_auth' => $courseAuth,
                'message' => 'CourseAuth granted successfully',
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to grant CourseAuth from order', [
                'order_item_id' => $orderItemId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to grant CourseAuth: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Revoke CourseAuth from order item
     */
    public function revokeFromOrder(int $orderItemId, string $reason): array
    {
        try {
            DB::beginTransaction();

            $courseAuths = CourseAuth::where('source_type', 'order')
                ->where('source_id', $orderItemId)
                ->get();

            if ($courseAuths->isEmpty()) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'No CourseAuth found for this order item',
                ];
            }

            $revokedCount = 0;
            foreach ($courseAuths as $courseAuth) {
                if (!$courseAuth->disabled_at) {
                    $courseAuth->update([
                        'disabled_at' => Carbon::now(),
                        'revoked_reason' => $reason,
                    ]);
                    $revokedCount++;
                }
            }

            DB::commit();

            Log::info('CourseAuth revoked from order', [
                'order_item_id' => $orderItemId,
                'revoked_count' => $revokedCount,
                'reason' => $reason,
            ]);

            return [
                'success' => true,
                'revoked_count' => $revokedCount,
                'message' => "Revoked {$revokedCount} CourseAuth(s)",
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to revoke CourseAuth from order', [
                'order_item_id' => $orderItemId,
                'reason' => $reason,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to revoke CourseAuth: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Grant manual CourseAuth (separate from orders)
     */
    public function grantManual(int $userId, int $courseId, ?\DateTime $expiresAt = null, ?int $createdBy = null): array
    {
        try {
            DB::beginTransaction();

            // Check for existing active manual CourseAuth
            $existingAuth = CourseAuth::where('user_id', $userId)
                ->where('course_id', $courseId)
                ->where('source_type', 'manual')
                ->where(function ($query) {
                    $query->whereNull('disabled_at')
                        ->whereNull('completed_at')
                        ->where(function ($q) {
                            $q->whereNull('expires_at')
                                ->orWhere('expires_at', '>', Carbon::now());
                        });
                })
                ->first();

            if ($existingAuth) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'User already has active manual CourseAuth for this course',
                    'existing_auth' => $existingAuth,
                ];
            }

            $courseAuth = $this->createCourseAuth([
                'user_id' => $userId,
                'course_id' => $courseId,
                'source_type' => 'manual',
                'source_id' => null,
                'starts_at' => Carbon::now(),
                'expires_at' => $expiresAt,
                'submitted_by' => $createdBy,
                'status' => 'active',
            ]);

            DB::commit();

            Log::info('Manual CourseAuth granted', [
                'course_auth_id' => $courseAuth->id,
                'user_id' => $userId,
                'course_id' => $courseId,
                'created_by' => $createdBy,
                'expires_at' => $expiresAt?->format('Y-m-d H:i:s'),
            ]);

            return [
                'success' => true,
                'course_auth' => $courseAuth,
                'message' => 'Manual CourseAuth granted successfully',
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to grant manual CourseAuth', [
                'user_id' => $userId,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to grant manual CourseAuth: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create CourseAuth record
     */
    private function createCourseAuth(array $data): CourseAuth
    {
        return CourseAuth::create([
            'user_id' => $data['user_id'],
            'course_id' => $data['course_id'],
            'source_type' => $data['source_type'],
            'source_id' => $data['source_id'],
            'starts_at' => $data['starts_at'],
            'expires_at' => $data['expires_at'],
            'submitted_by' => $data['submitted_by'] ?? null,
        ]);
    }

    /**
     * Calculate expiration date based on course duration
     */
    private function calculateExpiration(Course $course): ?\DateTime
    {
        // If course has specific duration, calculate expiration
        // This would depend on your course model structure
        // For now, return null (no expiration)
        return null;
    }
}
