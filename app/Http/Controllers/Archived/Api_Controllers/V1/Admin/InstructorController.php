<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // TODO: Implement with Instructor service and proper pagination
        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'Instructor management API - to be implemented',
            'meta' => [
                'total' => 0,
                'per_page' => 15,
                'current_page' => 1,
            ],
        ]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        // TODO: Implement single instructor details
        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Instructor details API - to be implemented',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        // TODO: Implement instructor creation
        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Instructor creation API - to be implemented',
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        // TODO: Implement instructor update
        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Instructor update API - to be implemented',
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        // TODO: Implement instructor deletion (soft delete)
        return response()->json([
            'success' => true,
            'message' => 'Instructor deletion API - to be implemented',
        ]);
    }
}
