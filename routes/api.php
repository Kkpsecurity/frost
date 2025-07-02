<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

*/

/**
 * OpenAI API Route
 */
Route::post('/openai', function (Request $request) {
    try {
        Log::info("🟢 OpenAI Request Received", ['request' => $request->all()]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'OpenAI-Organization' => env('OPENAI_ORG_ID'),
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $request->input('model', 'gpt-4'),
            'messages' => [
                ['role' => 'system', 'content' => $request->input('systemRole', 'You are an AI assistant. Greet by name if available. Suggest the next step')],
                ['role' => 'user', 'content' => $request->input('prompt')],
            ],
            'temperature' => $request->input('temperature', 0.7),
        ]);

        Log::info("✅ OpenAI Response", ['response' => $response->json()]);

        return response()->json($response->json());
    } catch (\Exception $e) {
        Log::error("❌ OpenAI API Error: " . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

/**
 * Write progress to a daily JSON file
 */
Route::post('/write-progress', function (Request $request) {
    try {
        // ✅ Define daily storage path
        $directory = 'aidata/admin';
        $date = now()->format('Y-m-d'); // Format: YYYY-MM-DD
        $fileName = "{$date}_progress.json";

        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory, 0775, true);
        }

        // Read existing progress file
        $progressData = [];
        if (Storage::exists("$directory/$fileName")) {
            $fileContents = Storage::get("$directory/$fileName");
            $progressData = json_decode($fileContents, true) ?? [];
        }

        if (!is_array($progressData)) {
            Log::warning("⚠️ Corrupt progress.json file detected. Resetting.");
            $progressData = [];
        }

        // Extract request payload
        $instructor = $request->input('instructor');
        $task = $request->input('task');
        $response = $request->input('response');
        $status = $request->input('status', 'active');
        $time = $request->input('time', now()->toDateTimeString());

        if (!$instructor || !$task || !$response) {
            return response()->json(['error' => 'Missing required fields'], 400);
        }

        // ✅ Avoid duplicate completed tasks
        foreach ($progressData as $entry) {
            if ($entry['instructor'] == $instructor && $entry['task'] == $task && $entry['status'] === 'completed') {
                Log::info("✅ Task already completed for Instructor $instructor.");
                return response()->json(['message' => 'Task already completed, skipping.'], 200);
            }
        }

        // ✅ Append new entry
        $newEntry = [
            'instructor' => $instructor,
            'task' => $task,
            'response' => $response,
            'status' => $status,
            'time' => $time,
        ];

        Log::info("🟢 Writing new progress entry:", $newEntry);
        $progressData[] = $newEntry;

        // Save to JSON file
        Storage::put("$directory/$fileName", json_encode($progressData, JSON_PRETTY_PRINT));

        return response()->json(['message' => 'Progress updated successfully']);
    } catch (\Exception $e) {
        Log::error("❌ Error writing progress.json: " . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

/**
 * Check progress from daily JSON file
 */
Route::post('/check-progress', function (Request $request) {
    try {
        $directory = 'aidata/admin';
        $date = now()->format('Y-m-d'); // Format: YYYY-MM-DD
        $fileName = "{$date}_progress.json";

        if (!Storage::exists("$directory/$fileName")) {
            Log::info("📁 No progress file found for $date.");
            return response()->json(['status' => 'not_found'], 200);
        }

        // ✅ Read progress data
        $fileContents = Storage::get("$directory/$fileName");
        $progressData = json_decode($fileContents, true) ?? [];

        if (!is_array($progressData)) {
            Log::warning("⚠️ Corrupt progress.json file detected.");
            return response()->json(['status' => 'not_found'], 200);
        }

        $instructor = $request->input('instructor');
        $task = $request->input('task');

        if (!$instructor || !$task) {
            return response()->json(['error' => 'Missing instructor or task field'], 400);
        }

        Log::info("🔎 Checking progress for Instructor: $instructor, Task: $task");

        // ✅ Search for matching instructor task
        foreach (array_reverse($progressData) as $entry) { // Reverse for latest entry
            if ($entry['instructor'] == $instructor && $entry['task'] == $task) {
                Log::info("✅ Found progress entry:", ['entry' => $entry]);

                return response()->json([
                    'status' => $entry['status'],
                    'response' => $entry['response'],
                    'time' => $entry['time'],
                ], 200);
            }
        }

        Log::info("❌ No progress found for Instructor: $instructor, Task: $task");
        return response()->json(['status' => 'not_found'], 200);
    } catch (\Exception $e) {
        Log::error("❌ Error checking progress.json: " . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

/**
 * Update progress entry
 */
Route::post('/update-progress', function (Request $request) {
    try {
        $directory = 'aidata/admin';
        $date = now()->format('Y-m-d'); // Format: YYYY-MM-DD
        $fileName = "{$date}_progress.json";

        if (!Storage::exists("$directory/$fileName")) {
            return response()->json(['error' => 'No progress file found'], 404);
        }

        // Read existing data
        $fileContents = Storage::get("$directory/$fileName");
        $progressData = json_decode($fileContents, true) ?? [];

        $instructor = $request->input('instructor');
        $task = $request->input('task');
        $status = $request->input('status');

        if (!$instructor || !$task || !$status) {
            return response()->json(['error' => 'Missing required fields'], 400);
        }

        // ✅ Find and update task
        foreach ($progressData as &$entry) {
            if ($entry['instructor'] == $instructor && $entry['task'] == $task) {
                $entry['status'] = $status;
                Log::info("✅ Progress updated:", ['entry' => $entry]);
                break;
            }
        }

        // Save back to file
        Storage::put("$directory/$fileName", json_encode($progressData, JSON_PRETTY_PRINT));

        return response()->json(['message' => 'Progress updated successfully']);
    } catch (\Exception $e) {
        Log::error("❌ Error updating progress.json: " . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
});





/**
 * Zoom API Route
 */

Route::post( '/zoom/signature', \App\Http\Controllers\API\ZoomSignature::class )
     ->name( 'api.zoom.signature' );


Route::post( '/agora/rtctoken', \App\Http\Controllers\API\AgoraRTCToken::class )
    ->name( 'api.agora.rtctoken' );

Route::post( '/agora/rtmtoken', \App\Http\Controllers\API\AgoraRTMToken::class )
    ->name( 'api.agora.rtmtoken' );
