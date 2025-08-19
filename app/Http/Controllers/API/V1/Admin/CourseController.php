<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // TODO: Implement with Course service and proper pagination
        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'Course management API - to be implemented',
            'meta' => [
                'total' => 0,
                'per_page' => 15,
                'current_page' => 1,
            ],
        ]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        // TODO: Implement single course details
        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Course details API - to be implemented',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        // TODO: Implement course creation
        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Course creation API - to be implemented',
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        // TODO: Implement course update
        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Course update API - to be implemented',
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        // TODO: Implement course deletion (soft delete)
        return response()->json([
            'success' => true,
            'message' => 'Course deletion API - to be implemented',
        ]);
    }
}
