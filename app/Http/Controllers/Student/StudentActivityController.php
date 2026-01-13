<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentActivityTracker;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class StudentActivityController extends Controller
{
    protected StudentActivityTracker $tracker;

    public function __construct(StudentActivityTracker $tracker)
    {
        $this->tracker = $tracker;
    }

    /**
     * Track site entry
     * POST /api/student/activity/site-entry
     */
    public function trackSiteEntry(Request $request): JsonResponse
    {
        $userId = Auth::id();

        $activity = $this->tracker->trackSiteEntry($userId, [
            'data' => [
                'landing_page' => $request->input('landing_page'),
            ],
        ]);

        return response()->json([
            'success' => true,
            'activity_id' => $activity?->id,
            'message' => 'Site entry tracked',
        ]);
    }

    /**
     * Track site exit
     * POST /api/student/activity/site-exit
     */
    public function trackSiteExit(Request $request): JsonResponse
    {
        $userId = Auth::id();

        $activity = $this->tracker->trackSiteExit($userId, [
            'data' => [
                'last_page' => $request->input('last_page'),
            ],
        ]);

        return response()->json([
            'success' => true,
            'activity_id' => $activity?->id,
            'message' => 'Site exit tracked',
        ]);
    }

    /**
     * Track classroom entry
     * POST /api/student/activity/classroom-entry
     */
    public function trackClassroomEntry(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'course_auth_id' => 'required|integer|exists:course_auths,id',
            'course_date_id' => 'required|integer|exists:course_dates,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = Auth::id();

        $activity = $this->tracker->trackClassroomEntry(
            $userId,
            $request->input('course_auth_id'),
            $request->input('course_date_id'),
            [
                'student_unit_id' => $request->input('student_unit_id'),
                'inst_unit_id' => $request->input('inst_unit_id'),
            ]
        );

        return response()->json([
            'success' => true,
            'activity_id' => $activity?->id,
            'message' => 'Classroom entry tracked',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Track agreement acceptance
     * POST /api/student/activity/agreement-accepted
     */
    public function trackAgreementAccepted(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'course_auth_id' => 'required|integer|exists:course_auths,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = Auth::id();

        $activity = $this->tracker->trackAgreementAccepted(
            $userId,
            $request->input('course_auth_id'),
            [
                'data' => [
                    'agreement_version' => $request->input('agreement_version'),
                    'agreement_type' => $request->input('agreement_type', 'course_terms'),
                ],
            ]
        );

        return response()->json([
            'success' => true,
            'activity_id' => $activity?->id,
            'message' => 'Agreement acceptance tracked',
        ]);
    }

    /**
     * Track rules acceptance
     * POST /api/student/activity/rules-accepted
     */
    public function trackRulesAccepted(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'student_unit_id' => 'required|integer|exists:student_units,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = Auth::id();

        $activity = $this->tracker->trackRulesAccepted(
            $userId,
            $request->input('student_unit_id')
        );

        return response()->json([
            'success' => true,
            'activity_id' => $activity?->id,
            'message' => 'Rules acceptance tracked',
        ]);
    }

    /**
     * Track tab visibility change
     * POST /api/student/activity/tab-visibility
     */
    public function trackTabVisibility(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'is_visible' => 'required|boolean',
            'hidden_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = Auth::id();
        $isVisible = $request->input('is_visible');

        if ($isVisible) {
            // Tab became visible (student returned)
            $hiddenAt = $request->input('hidden_at') ? Carbon::parse($request->input('hidden_at')) : null;
            $activity = $this->tracker->trackTabVisible($userId, $hiddenAt);
            $message = 'Tab visibility tracked (returned to site)';
        } else {
            // Tab became hidden (student left)
            $activity = $this->tracker->trackTabHidden($userId);
            $message = 'Tab visibility tracked (left site)';
        }

        return response()->json([
            'success' => true,
            'activity_id' => $activity?->id,
            'message' => $message,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Track button click
     * POST /api/student/activity/button-click
     */
    public function trackButtonClick(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'button_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = Auth::id();

        $activity = $this->tracker->trackButtonClick(
            $userId,
            $request->input('button_name'),
            [
                'course_auth_id' => $request->input('course_auth_id'),
                'data' => [
                    'button_text' => $request->input('button_text'),
                    'page' => $request->input('page'),
                ],
            ]
        );

        return response()->json([
            'success' => true,
            'activity_id' => $activity?->id,
            'message' => 'Button click tracked',
        ]);
    }

    /**
     * Get student activity timeline
     * GET /api/student/activity/timeline
     */
    public function getTimeline(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : today();

        $timeline = $this->tracker->getTimeline($userId, $date);

        return response()->json([
            'success' => true,
            'date' => $date->toDateString(),
            'timeline' => $timeline,
            'count' => count($timeline),
        ]);
    }

    /**
     * Get time away from site (audit report)
     * GET /api/student/activity/away-time
     */
    public function getAwayTime(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $sessionId = $request->input('session_id', session()->getId());

        $awayTime = $this->tracker->calculateAwayTime($userId, $sessionId);

        return response()->json([
            'success' => true,
            'session_id' => $sessionId,
            'away_time' => $awayTime,
        ]);
    }
}
