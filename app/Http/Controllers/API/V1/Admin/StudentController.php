<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // TODO: Implement with Student service and proper pagination
        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'Student management API - to be implemented',
            'meta' => [
                'total' => 0,
                'per_page' => 15,
                'current_page' => 1,
            ],
        ]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        // TODO: Implement single student details
        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Student details API - to be implemented',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        // TODO: Implement student creation
        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Student creation API - to be implemented',
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        // TODO: Implement student update
        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Student update API - to be implemented',
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        // TODO: Implement student deletion (soft delete)
        return response()->json([
            'success' => true,
            'message' => 'Student deletion API - to be implemented',
        ]);
    }
}
