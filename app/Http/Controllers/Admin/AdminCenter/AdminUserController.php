<?php

namespace App\Http\Controllers\Admin\AdminCenter;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use App\Models\Admin;
use App\Models\Role;
use App\Support\RoleManager;

class AdminUserController extends Controller
{
    /**
     * Display a listing of admin users with DataTables
     */
    public function index(Request $request)
    {
        // Handle AJAX request for DataTables
        if ($request->ajax()) {
            return $this->getData($request);
        }

        // Return the view for regular requests
        return view('admin.admin-center.admin-users.index');
    }

    /**
     * Get admin users data for DataTables
     */
    public function getData(Request $request): JsonResponse
    {
        $query = Admin::with('Role');

        // Apply role filter if provided
        if ($request->has('role_filter') && !empty($request->role_filter)) {
            $query->whereHas('Role', function($q) use ($request) {
                $q->where('name', $request->role_filter);
            });
        }

        return DataTables::of($query)
            ->addColumn('name', function ($admin) {
                return $admin->fname . ' ' . $admin->lname;
            })
            ->addColumn('role', function ($admin) {
                return $admin->Role ? $admin->Role->name : 'N/A';
            })
            ->addColumn('status', function ($admin) {
                return $admin->is_active ?
                    '<span class="badge badge-success">Active</span>' :
                    '<span class="badge badge-danger">Inactive</span>';
            })
            ->addColumn('role_badge', function ($admin) {
                $roleName = $admin->Role->name ?? null;
                if ($roleName) {
                    $badgeClass = RoleManager::getRoleBadgeClass($roleName);
                    $displayName = RoleManager::getDisplayName($roleName);
                    return '<span class="badge ' . $badgeClass . '">' . $displayName . '</span>';
                }
                return '<span class="badge badge-light">N/A</span>';
            })
            ->addColumn('avatar_display', function ($admin) {
                if ($admin->avatar) {
                    return '<img src="' . asset('storage/' . $admin->avatar) . '" class="img-circle" width="32" height="32">';
                } elseif ($admin->use_gravatar) {
                    $gravatar = 'https://www.gravatar.com/avatar/' . md5(strtolower($admin->email)) . '?s=32&d=identicon';
                    return '<img src="' . $gravatar . '" class="img-circle" width="32" height="32">';
                }
                return '<i class="fas fa-user-circle fa-2x text-muted"></i>';
            })
            ->editColumn('created_at', function ($admin) {
                return $admin->created_at ? $admin->created_at->format('M d, Y') : 'N/A';
            })
            ->addColumn('action', function ($admin) {
                return '
                    <div class="btn-group" role="group">
                        <a href="' . route('admin.admin-center.admin-users.show', $admin->id) . '" class="btn btn-sm btn-info" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="' . route('admin.admin-center.admin-users.edit', $admin->id) . '" class="btn btn-sm btn-primary" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger" title="Delete" onclick="deleteAdmin(' . $admin->id . ')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['status', 'role', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new admin user
     */
    public function create(): View
    {
        $roles = Role::whereIn('id', RoleManager::getAdminRoleIds())->get();
        return view('admin.admin-center.admin-users.create', compact('roles'));
    }

    /**
     * Store a newly created admin user
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role_id' => 'required|integer|exists:roles,id|lte:4',
            'is_active' => 'boolean',
            'use_gravatar' => 'boolean',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $adminData = [
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'is_active' => $request->boolean('is_active', true),
            'use_gravatar' => $request->boolean('use_gravatar', false),
            'email_verified_at' => now(),
        ];

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $adminData['avatar'] = $avatarPath;
        }

        Admin::create($adminData);

        return redirect()->route('admin.admin-center.admin-users.index')
            ->with('success', 'Admin user created successfully.');
    }

    /**
     * Display the specified admin user
     */
    public function show(string $id): View
    {
        $admin = Admin::with('Role')->findOrFail($id);
        return view('admin.admin-center.admin-users.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified admin user
     */
    public function edit(string $id): View
    {
        $admin = Admin::findOrFail($id);
        $roles = Role::whereIn('id', RoleManager::getAdminRoleIds())->get();
        return view('admin.admin-center.admin-users.edit', compact('admin', 'roles'));
    }

    /**
     * Update the specified admin user
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $admin = Admin::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role_id' => 'required|integer|exists:roles,id|lte:4',
            'is_active' => 'boolean',
            'use_gravatar' => 'boolean',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $adminData = [
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'is_active' => $request->boolean('is_active'),
            'use_gravatar' => $request->boolean('use_gravatar'),
        ];

        // Handle password update
        if ($request->filled('password')) {
            $adminData['password'] = Hash::make($request->password);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($admin->avatar) {
                Storage::disk('public')->delete($admin->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $adminData['avatar'] = $avatarPath;
        }

        // Handle avatar removal
        if ($request->boolean('remove_avatar')) {
            if ($admin->avatar) {
                Storage::disk('public')->delete($admin->avatar);
            }
            $adminData['avatar'] = null;
        }

        $admin->update($adminData);

        return redirect()->route('admin.admin-center.admin-users.index')
            ->with('success', 'Admin user updated successfully.');
    }

    /**
     * Remove the specified admin user
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $admin = Admin::findOrFail($id);

            // Prevent deletion of system admin (role_id = 1)
            if ($admin->role_id === 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete system administrator.'
                ], 403);
            }

            // Delete avatar file if exists
            if ($admin->avatar) {
                Storage::disk('public')->delete($admin->avatar);
            }

            $admin->delete();

            return response()->json([
                'success' => true,
                'message' => 'Admin user deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting admin user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update admin user password
     */
    public function updatePassword(Request $request, string $id): RedirectResponse
    {
        $admin = Admin::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'password')
                ->withInput();
        }

        $admin->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->back()
            ->with('password_success', 'Password updated successfully.');
    }

    /**
     * Update admin user avatar
     */
    public function updateAvatar(Request $request, string $id): RedirectResponse
    {
        $admin = Admin::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'use_gravatar' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'avatar')
                ->withInput();
        }

        $updateData = [
            'use_gravatar' => $request->boolean('use_gravatar')
        ];

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($admin->avatar) {
                Storage::disk('public')->delete($admin->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $updateData['avatar'] = $avatarPath;
        }

        // Handle avatar removal
        if ($request->boolean('remove_avatar')) {
            if ($admin->avatar) {
                Storage::disk('public')->delete($admin->avatar);
            }
            $updateData['avatar'] = null;
        }

        $admin->update($updateData);

        return redirect()->back()
            ->with('avatar_success', 'Avatar updated successfully.');
    }

    /**
     * Impersonate a user (only for sys admins)
     */
    public function impersonate(int $userId): RedirectResponse
    {
        $currentUser = auth()->user();
        $currentUserRole = $currentUser && $currentUser->Role ? $currentUser->Role->name : null;

        // Check if current user is sys admin
        if (!$currentUserRole || !RoleManager::canImpersonate($currentUserRole)) {
            abort(403, 'Unauthorized. Only system administrators can impersonate users.');
        }

        // Prevent self-impersonation
        if ($currentUser->id === $userId) {
            return redirect()->back()->with('error', 'You cannot impersonate yourself.');
        }

        $userToImpersonate = Admin::findOrFail($userId);
        $targetUserRole = $userToImpersonate->Role ? $userToImpersonate->Role->name : null;

        // Check if target user can be impersonated (not sys admin)
        if (!$targetUserRole || !RoleManager::canBeImpersonated($targetUserRole)) {
            return redirect()->back()->with('error', 'This user cannot be impersonated.');
        }

        // Start impersonation
        $currentUser->impersonate($userToImpersonate);

        return redirect()->route('admin.dashboard')
            ->with('success', 'You are now impersonating: ' . $userToImpersonate->fname . ' ' . $userToImpersonate->lname);
    }

    /**
     * Stop impersonating and return to original user
     */
    public function stopImpersonating(): RedirectResponse
    {
        $user = auth()->user();

        if ($user->isImpersonated()) {
            $user->leaveImpersonation();
            return redirect()->route('admin.admin-center.admin-users.index')
                ->with('success', 'You have stopped impersonating and returned to your original account.');
        }

        return redirect()->route('admin.dashboard')
            ->with('error', 'You are not currently impersonating anyone.');
    }

    /**
     * Deactivate an admin user
     */
    public function deactivate(int $id): RedirectResponse
    {
        $admin = Admin::findOrFail($id);

        // Prevent self-deactivation
        if ($admin->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        // Prevent deactivating sys admins (unless current user is also sys admin)
        $currentUserRole = auth()->user()->Role ? auth()->user()->Role->name : null;
        $targetUserRole = $admin->Role ? $admin->Role->name : null;

        if ($targetUserRole === RoleManager::SYS_ADMIN_NAME && $currentUserRole !== RoleManager::SYS_ADMIN_NAME) {
            return redirect()->back()->with('error', 'Only system administrators can deactivate other system administrators.');
        }

        // Update the user's active status
        $admin->update(['is_active' => false]);

        return redirect()->route('admin.admin-center.admin-users.show', $admin->id)
            ->with('success', 'User ' . $admin->fname . ' ' . $admin->lname . ' has been deactivated successfully.');
    }

    /**
     * Activate an admin user
     */
    public function activate(int $id): RedirectResponse
    {
        $admin = Admin::findOrFail($id);

        // Update the user's active status
        $admin->update(['is_active' => true]);

        return redirect()->route('admin.admin-center.admin-users.show', $admin->id)
            ->with('success', 'User ' . $admin->fname . ' ' . $admin->lname . ' has been activated successfully.');
    }
}
