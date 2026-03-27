<?php

namespace App\Http\Controllers\Admin\AdminCenter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Role;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\CourseUnitLesson;
use App\Models\DiscountCode;
use App\Models\Exam;
use App\Models\ExamQuestionSpec;
use App\Models\Lesson;
use App\Models\PaymentType;
use App\Models\SiteConfig;
use App\Services\Payments\PayPalConfigService;
use App\Services\RCache;

class AdminCenterController extends Controller
{
    /**
     * Admin Users Management
     */
    public function adminUsers()
    {
        $adminUsers = User::whereIn('role_id', [1, 2]) // SysAdmin and Administrator
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.admin-center.admin-users', compact('adminUsers'));
    }

    /**
     * Show Admin User Details
     */
    public function showAdminUser($id)
    {
        $admin = User::whereIn('role_id', [1, 2])->findOrFail($id);

        return view('admin.admin-center.admin-users-show', compact('admin'));
    }

    /**
     * Create Admin User Form
     */
    public function createAdminUser()
    {
        $roles = Role::whereIn('id', [1, 2])->get();
        return view('admin.admin-center.admin-users-create', compact('roles'));
    }

    /**
     * Store New Admin User
     */
    public function storeAdminUser(Request $request)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|in:1,2',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            $user = User::create([
                'fname' => $validated['fname'],
                'lname' => $validated['lname'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $validated['role_id'],
                'phone' => $validated['phone'] ?? null,
                'is_active' => true,
            ]);

            Log::info('Admin user created', ['user_id' => $user->id, 'created_by' => auth()->id()]);

            return redirect()->route('admin.admin-center.admin-users')
                ->with('success', 'Admin user created successfully');
        } catch (\Exception $e) {
            Log::error('Failed to create admin user', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Failed to create admin user: ' . $e->getMessage());
        }
    }

    /**
     * Edit Admin User Form
     */
    public function editAdminUser($id)
    {
        $admin = User::whereIn('role_id', [1, 2])->findOrFail($id);
        $roles = Role::whereIn('id', [1, 2])->get();

        return view('admin.admin-center.admin-users-edit', compact('admin', 'roles'));
    }

    /**
     * Update Admin User
     */
    public function updateAdminUser(Request $request, $id)
    {
        $admin = User::whereIn('role_id', [1, 2])->findOrFail($id);

        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|in:1,2',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        try {
            $admin->update($validated);

            // Update password if provided
            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'string|min:8|confirmed',
                ]);
                $admin->update(['password' => Hash::make($request->password)]);
            }

            Log::info('Admin user updated', ['user_id' => $admin->id, 'updated_by' => auth()->id()]);

            return redirect()->route('admin.admin-center.admin-users')
                ->with('success', 'Admin user updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update admin user', ['user_id' => $id, 'error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Failed to update admin user: ' . $e->getMessage());
        }
    }

    /**
     * Delete Admin User
     */
    public function deleteAdminUser($id)
    {
        try {
            $admin = User::whereIn('role_id', [1, 2])->findOrFail($id);

            // Prevent deleting yourself
            if ($admin->id == auth()->id()) {
                return back()->with('error', 'You cannot delete your own account');
            }

            // Prevent deleting the last SysAdmin
            if ($admin->role_id == 1) {
                $sysAdminCount = User::where('role_id', 1)->count();
                if ($sysAdminCount <= 1) {
                    return back()->with('error', 'Cannot delete the last System Administrator');
                }
            }

            $adminName = $admin->fullname();
            $admin->delete();

            Log::warning('Admin user deleted', [
                'user_id' => $id,
                'user_name' => $adminName,
                'deleted_by' => auth()->id()
            ]);

            return redirect()->route('admin.admin-center.admin-users')
                ->with('success', 'Admin user deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete admin user', ['user_id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to delete admin user: ' . $e->getMessage());
        }
    }

    /**
     * Toggle Admin Status
     */
    public function toggleAdminStatus($id)
    {
        try {
            $admin = User::whereIn('role_id', [1, 2])->findOrFail($id);

            // Prevent deactivating yourself
            if ($admin->id == auth()->id()) {
                return back()->with('error', 'You cannot deactivate your own account');
            }

            $admin->is_active = !$admin->is_active;
            $admin->save();

            $status = $admin->is_active ? 'activated' : 'deactivated';
            Log::info("Admin user {$status}", ['user_id' => $id, 'changed_by' => auth()->id()]);

            return back()->with('success', "Admin user {$status} successfully");
        } catch (\Exception $e) {
            Log::error('Failed to toggle admin status', ['user_id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to change admin status: ' . $e->getMessage());
        }
    }

    /**
     * Change Admin Role
     */
    public function changeAdminRole(Request $request, $id)
    {
        $validated = $request->validate([
            'role_id' => 'required|in:1,2',
        ]);

        try {
            $admin = User::whereIn('role_id', [1, 2])->findOrFail($id);

            // Prevent changing your own role
            if ($admin->id == auth()->id()) {
                return back()->with('error', 'You cannot change your own role');
            }

            // Prevent demoting the last SysAdmin
            if ($admin->role_id == 1 && $validated['role_id'] != 1) {
                $sysAdminCount = User::where('role_id', 1)->count();
                if ($sysAdminCount <= 1) {
                    return back()->with('error', 'Cannot change role of the last System Administrator');
                }
            }

            $oldRole = $admin->role_id;
            $admin->role_id = $validated['role_id'];
            $admin->save();

            Log::info('Admin role changed', [
                'user_id' => $id,
                'old_role' => $oldRole,
                'new_role' => $validated['role_id'],
                'changed_by' => auth()->id()
            ]);

            return back()->with('success', 'Admin role changed successfully');
        } catch (\Exception $e) {
            Log::error('Failed to change admin role', ['user_id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to change admin role: ' . $e->getMessage());
        }
    }

    /**
     * Instructor Management
     */
    public function instructorManagement()
    {
        $instructors = User::where('role_id', 4) // Instructor role
            ->with(['instUnits' => function ($query) {
                $query->latest()->limit(5);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_instructors' => User::where('role_id', 4)->count(),
            'active_instructors' => User::where('role_id', 4)->where('is_active', true)->count(),
            'total_classes_taught' => DB::table('inst_unit')->distinct('created_by')->count(),
        ];

        return view('admin.admin-center.instructor-management', compact('instructors', 'stats'));
    }

    /**
     * Show Instructor Details
     */
    public function showInstructor($id)
    {
        $instructor = User::where('role_id', 4)
            ->with(['instUnits', 'roles'])
            ->findOrFail($id);

        return view('admin.admin-center.instructor-show', compact('instructor'));
    }

    /**
     * Edit Instructor
     */
    public function editInstructor($id)
    {
        $instructor = User::where('role_id', 4)
            ->with(['instUnits', 'roles'])
            ->findOrFail($id);

        $roles = Role::all();

        return view('admin.admin-center.instructor-edit', compact('instructor', 'roles'));
    }

    /**
     * Update Instructor
     */
    public function updateInstructor(Request $request, $id)
    {
        $instructor = User::where('role_id', 4)->findOrFail($id);

        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'nullable|boolean',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        try {
            // Update basic info
            $instructor->fname = $validated['fname'];
            $instructor->lname = $validated['lname'];
            $instructor->email = $validated['email'];
            $instructor->is_active = $request->has('is_active');

            // Update phone in student_info JSON
            if (isset($validated['phone'])) {
                $studentInfo = $instructor->student_info ?? [];
                $studentInfo['phone'] = $validated['phone'];
                $instructor->student_info = $studentInfo;
            }

            // Update password if provided
            if (!empty($validated['password'])) {
                $instructor->password = Hash::make($validated['password']);
            }

            $instructor->save();

            // Sync roles if provided
            if ($request->has('roles')) {
                $instructor->roles()->sync($request->roles);
            }

            Log::info('Instructor updated successfully', ['instructor_id' => $id, 'updated_by' => auth()->id()]);

            return back()->with('success', 'Instructor updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to update instructor', ['instructor_id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to update instructor: ' . $e->getMessage());
        }
    }

    /**
     * Toggle Instructor Status
     */
    public function toggleInstructorStatus($id)
    {
        try {
            $instructor = User::where('role_id', 4)->findOrFail($id);
            $instructor->is_active = !$instructor->is_active;
            $instructor->save();

            $status = $instructor->is_active ? 'activated' : 'deactivated';
            Log::info("Instructor {$status}", ['instructor_id' => $id, 'updated_by' => auth()->id()]);

            return back()->with('success', "Instructor has been {$status} successfully!");
        } catch (\Exception $e) {
            Log::error('Failed to toggle instructor status', ['instructor_id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to update instructor status: ' . $e->getMessage());
        }
    }

    /**
     * Role Permissions
     */
    public function rolePermissions()
    {
        $roles = Role::all();

        return view('admin.admin-center.role-permissions', compact('roles'));
    }

    /**
     * Payment Gateway Settings
     */
    public function paymentGateway()
    {
        $payPalConfig = app(PayPalConfigService::class);

        $settings = [
            'stripe_enabled' => config('services.stripe.enabled', false),
            'stripe_key' => config('services.stripe.key'),
            'paypal_enabled' => config('services.paypal.enabled', false),
            'paypal_client_id' => $payPalConfig->clientId(),
        ];

        return view('admin.admin-center.payment-gateway', compact('settings'));
    }

    /**
     * Update Payment Gateway Settings
     */
    public function updatePaymentGateway(Request $request)
    {
        // Implementation for updating payment gateway settings
        return redirect()->back()->with('success', 'Payment gateway settings updated successfully');
    }

    /**
     * Transaction Logs
     */
    public function transactionLogs()
    {
        $transactions = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', DB::raw('CONCAT(users.fname, \' \', users.lname) as user_name'))
            ->orderBy('orders.created_at', 'desc')
            ->paginate(50);

        return view('admin.admin-center.transaction-logs', compact('transactions'));
    }

    /**
     * Payment Methods
     */
    public function paymentMethods()
    {
        $paymentMethods = DB::table('orders')
            ->join('payment_types', 'orders.payment_type_id', '=', 'payment_types.id')
            ->select(
                'payment_types.id',
                'payment_types.name',
                DB::raw('COUNT(orders.id) as count'),
                DB::raw('SUM(orders.total_price) as total'),
                DB::raw('AVG(orders.total_price) as average')
            )
            ->whereNotNull('orders.completed_at')
            ->groupBy('payment_types.id', 'payment_types.name')
            ->orderBy('count', 'desc')
            ->get();

        $stats = [
            'total_orders' => DB::table('orders')->whereNotNull('completed_at')->count(),
            'total_revenue' => DB::table('orders')->whereNotNull('completed_at')->sum('total_price'),
            'total_methods' => DB::table('payment_types')->where('is_active', true)->count(),
        ];

        return view('admin.admin-center.payment-methods', compact('paymentMethods', 'stats'));
    }

    /**
     * General Settings
     *
     * Settings are stored with dot-notation keys (e.g. "app.name", "auth.timeout").
     * The 'group' column may not exist; filter by key prefix instead.
     */
    public function generalSettings()
    {
        $allSettings = DB::table('settings')->get();

        // Detect whether a 'group' column exists on this DB
        $hasGroupCol = $allSettings->isNotEmpty() && property_exists($allSettings->first(), 'group');

        if ($hasGroupCol) {
            $appSettings    = $allSettings->where('group', 'app')->values();
            $authSettings   = $allSettings->where('group', 'auth')->values();
            $systemSettings = $allSettings->whereIn('group', ['system', 'mail', 'cache'])->values();
        } else {
            // Keys stored as dot-notation: "app.name", "auth.timeout", etc.
            $appSettings    = $allSettings->filter(fn($s) => str_starts_with($s->key, 'app.'))->values();
            $authSettings   = $allSettings->filter(fn($s) => str_starts_with($s->key, 'auth.'))->values();
            $systemSettings = $allSettings->filter(
                fn($s) =>
                str_starts_with($s->key, 'system.') ||
                    str_starts_with($s->key, 'mail.')   ||
                    str_starts_with($s->key, 'cache.')
            )->values();
        }

        return view('admin.admin-center.general-settings', compact('appSettings', 'authSettings', 'systemSettings'));
    }

    /**
     * Update General Settings
     */
    public function updateGeneralSettings(Request $request)
    {
        // Implementation for updating general settings
        return redirect()->back()->with('success', 'General settings updated successfully');
    }

    /**
     * Email Templates
     */
    public function emailTemplates()
    {
        return view('admin.admin-center.email-templates');
    }

    /**
     * Notifications
     */
    public function notifications()
    {
        return view('admin.admin-center.notifications');
    }

    /**
     * Activity Logs — recent sessions joined with user info
     */
    public function activityLogs()
    {
        // Join sessions with users to show recent active sessions
        $activities = DB::table('sessions')
            ->leftJoin('users', 'users.id', '=', 'sessions.user_id')
            ->select(
                'sessions.id as session_id',
                'sessions.user_id',
                'sessions.ip_address',
                'sessions.user_agent',
                'sessions.last_activity',
                'users.fname',
                'users.lname',
                'users.email',
                'users.role_id',
                'users.is_active'
            )
            ->orderBy('sessions.last_activity', 'desc')
            ->limit(150)
            ->get()
            ->map(function ($row) {
                $row->last_activity_at = \Carbon\Carbon::createFromTimestamp($row->last_activity);
                return $row;
            });

        // Active session count (last 30 min)
        $activeSince   = now()->subMinutes(30)->timestamp;
        $activeCount   = $activities->where('last_activity', '>=', $activeSince)->count();
        $guestCount    = $activities->whereNull('user_id')->count();
        $authCount     = $activities->whereNotNull('user_id')->count();

        return view(
            'admin.admin-center.activity-logs',
            compact('activities', 'activeCount', 'guestCount', 'authCount')
        );
    }

    /**
     * Login Attempts
     */
    public function loginAttempts()
    {
        return view('admin.admin-center.login-attempts');
    }

    /**
     * IP Whitelist
     */
    public function ipWhitelist()
    {
        return view('admin.admin-center.ip-whitelist');
    }

    /**
     * Database Tools
     */
    public function databaseTools()
    {
        $connName = config('database.default');
        $dbInfo = [
            'connection' => $connName,
            'driver'     => config("database.connections.{$connName}.driver", $connName),
            'host'       => config("database.connections.{$connName}.host", 'localhost'),
            'port'       => config("database.connections.{$connName}.port", ''),
            'database'   => config("database.connections.{$connName}.database"),
            'tables'     => [],
            'error'      => null,
        ];

        try {
            // Table list with row estimates and sizes (PostgreSQL)
            $tables = DB::select("
                SELECT
                    t.table_name,
                    COALESCE(s.n_live_tup, 0)                         AS row_estimate,
                    pg_total_relation_size(c.oid)                      AS total_bytes,
                    pg_size_pretty(pg_total_relation_size(c.oid))      AS total_size,
                    pg_size_pretty(pg_relation_size(c.oid))            AS table_size,
                    pg_size_pretty(pg_indexes_size(c.oid))             AS index_size
                FROM information_schema.tables t
                LEFT JOIN pg_stat_user_tables s ON s.relname = t.table_name
                LEFT JOIN pg_class c            ON c.relname = t.table_name
                WHERE t.table_schema = 'public'
                  AND t.table_type  = 'BASE TABLE'
                ORDER BY t.table_name
            ");
            $dbInfo['tables'] = collect($tables)->map(fn($r) => (array) $r)->toArray();

            // DB-level summary
            $dbStats = DB::selectOne("
                SELECT
                    pg_size_pretty(pg_database_size(current_database())) AS db_size,
                    (SELECT count(*) FROM information_schema.tables
                      WHERE table_schema = 'public' AND table_type = 'BASE TABLE') AS table_count,
                    version() AS pg_version
            ");
            $dbInfo['db_size']    = $dbStats->db_size    ?? '—';
            $dbInfo['table_count'] = $dbStats->table_count ?? 0;
            $dbInfo['pg_version'] = $dbStats->pg_version  ?? '—';
        } catch (\Exception $e) {
            $dbInfo['error'] = $e->getMessage();
        }

        return view('admin.admin-center.database-tools', compact('dbInfo'));
    }

    /**
     * Cache Management
     */
    public function cacheManagement()
    {
        $cacheDriver = config('cache.default');

        $modelClasses = self::_rcacheModelMap();

        $redis = RCache::Redis();
        $rcacheModels = [];
        $staticModels = [ExamQuestionSpec::class, PaymentType::class, Role::class];

        foreach ($modelClasses as $name => $class) {
            $inRedis = $redis ? (bool) $redis->exists($class) : false;
            $count   = ($inRedis && $redis) ? $redis->hLen($class) : 0;
            $rcacheModels[] = [
                'name'    => $name,
                'class'   => $class,
                'inRedis' => $inRedis,
                'count'   => $count,
                'static'  => in_array($class, $staticModels),
            ];
        }

        $redisMemory = null;
        try {
            if ($redis) {
                $redisMemory = RCache::RedisMemory();
            }
        } catch (\Exception $e) {
            // silently skip if redis is unavailable
        }

        return view(
            'admin.admin-center.cache-management',
            compact('cacheDriver', 'rcacheModels', 'redisMemory')
        );
    }

    /**
     * Reload RCache — force DB reload of one or all models into Redis
     */
    public function reloadRCache(Request $request)
    {
        $model        = $request->input('model', 'all');
        $modelClasses = self::_rcacheModelMap();

        try {
            if ($model === 'all') {
                foreach ($modelClasses as $class) {
                    RCache::LoadModelCache($class, true);
                }
                $msg = 'All RCache models reloaded from database.';
            } elseif (isset($modelClasses[$model])) {
                RCache::LoadModelCache($modelClasses[$model], true);
                $msg = "{$model} cache reloaded from database.";
            } else {
                return redirect()->back()->with('error', "Unknown model: {$model}");
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'RCache reload failed: ' . $e->getMessage());
        }
    }

    /**
     * Model class map shared by cache management actions
     */
    private static function _rcacheModelMap(): array
    {
        return [
            'Courses'           => Course::class,
            'CourseUnits'       => CourseUnit::class,
            'CourseUnitLessons' => CourseUnitLesson::class,
            'DiscountCodes'     => DiscountCode::class,
            'Exams'             => Exam::class,
            'ExamQuestionSpecs' => ExamQuestionSpec::class,
            'Lessons'           => Lesson::class,
            'PaymentTypes'      => PaymentType::class,
            'Roles'             => Role::class,
            'SiteConfigs'       => SiteConfig::class,
        ];
    }

    /**
     * Clear Cache
     */
    public function clearCache(Request $request)
    {
        $type = $request->input('type', 'all');

        try {
            switch ($type) {
                case 'config':
                    Artisan::call('config:clear');
                    break;
                case 'route':
                    Artisan::call('route:clear');
                    break;
                case 'view':
                    Artisan::call('view:clear');
                    break;
                default:
                    Artisan::call('cache:clear');
                    Artisan::call('config:clear');
                    Artisan::call('route:clear');
                    Artisan::call('view:clear');
            }

            return redirect()->back()->with('success', 'Cache cleared successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * System Health
     */
    public function systemHealth()
    {
        $health = [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'database' => 'Connected',
            'cache' => 'Working',
            'storage_writable' => is_writable(storage_path()),
        ];

        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $health['database'] = 'Error: ' . $e->getMessage();
        }

        try {
            Cache::put('health_check', true, 60);
            if (!Cache::get('health_check')) {
                $health['cache'] = 'Not Working';
            }
        } catch (\Exception $e) {
            $health['cache'] = 'Error: ' . $e->getMessage();
        }

        return view('admin.admin-center.system-health', compact('health'));
    }
}
