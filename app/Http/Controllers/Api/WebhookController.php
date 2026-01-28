<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle n8n callback
     *
     * POST /api/webhooks/n8n/callback
     */
    public function n8nCallback(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            Log::info('n8n webhook callback received', [
                'data' => $data,
                'headers' => $request->headers->all(),
            ]);

            // Process callback data
            // You can update event status, trigger actions, etc.

            if (isset($data['event_id'])) {
                // Update event with callback data
                // SentinelEvent::where('id', $data['event_id'])->update([...]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Callback processed',
            ]);
        } catch (\Exception $e) {
            Log::error('n8n webhook callback failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Callback processing failed',
            ], 500);
        }
    }

    /**
     * Handle n8n notification
     *
     * POST /api/webhooks/n8n/notify
     */
    public function n8nNotify(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            Log::info('n8n notification received', [
                'data' => $data,
            ]);

            // Handle notification
            // Send email, create alert, etc.

            return response()->json([
                'success' => true,
                'message' => 'Notification received',
            ]);
        } catch (\Exception $e) {
            Log::error('n8n notification failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Notification processing failed',
            ], 500);
        }
    }
}
