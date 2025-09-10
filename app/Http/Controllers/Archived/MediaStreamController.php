<?php

namespace App\Http\Controllers;

use App\Models\MediaFile;
use App\Services\StreamingService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MediaStreamController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private StreamingService $streamingService
    ) {}

    /**
     * Stream file with authentication and range support
     */
    public function stream(Request $request, MediaFile $file)
    {
        // Basic access check - can be enhanced with policies later
        if (!$this->canAccessFile($file)) {
            abort(403, 'Access denied');
        }

        return $this->streamingService->streamWithRange(
            $file,
            $request->header('Range')
        );
    }

    /**
     * Basic access control - to be enhanced with proper policies
     */
    private function canAccessFile(MediaFile $file): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        // Admin can access everything
        if ($this->isAdmin($user)) {
            return true;
        }

        // Users can access their own files
        if ($file->user_id === $user->id) {
            return true;
        }

        // Public disk files are accessible to authenticated users
        if ($file->disk === 'public') {
            return true;
        }

        // Local and S3 files require specific permissions
        return false;
    }

    /**
     * Check if user is admin
     */
    private function isAdmin($user): bool
    {
        if (isset($user->role) && $user->role) {
            return strtolower($user->role->name ?? '') === 'admin';
        }

        if (isset($user->role_id)) {
            return $user->role_id === 1; // Assuming 1 is admin
        }

        return false;
    }
}
