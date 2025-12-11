<?php

namespace App\Http\Controllers\Admin\AdminCenter;

/**
 * SettingsController
 * Handles CRUD operations for admin settings
 */

use Illuminate\Http\Request;
use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Services\SiteConfigService;
use Akaunting\Setting\Facade as Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Display a listing of the settings.
     */
    public function index()
    {
        // Get all settings directly from database with normalized structure
        $rawSettings = DB::table('settings')->get();

        // Create a flat structure for the datatable using the normalized columns
        $settingsForTable = [];
        foreach ($rawSettings as $setting) {
            $settingsForTable[] = [
                'key' => $setting->key, // Use actual key, not reconstructed
                'value' => $setting->value,
                'group' => $setting->group,
                'full_key' => $setting->group . '.' . $setting->key // For reference if needed
            ];
        }

        // Create grouped structure for filter dropdown using normalized columns
        $groupedSettings = [];
        foreach ($rawSettings as $setting) {
            $groupedSettings[$setting->group][$setting->key] = $setting->value;
        }

        return view('admin.settings.index', compact('settingsForTable', 'groupedSettings'));
    }

    /**
     * Show the form for creating a new setting.
     */
    public function create()
    {
        return view('admin.settings.create');
    }

    /**
     * Store a newly created setting.
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required',
            'prefix' => 'nullable|string|max:100',
        ]);

        $key = $request->prefix ? $request->prefix . '.' . $request->key : $request->key;

        Setting::set($key, $request->value);

        return redirect()->route('admin.settings.index')
                        ->with('success', 'Setting created successfully.');
    }

    /**
     * Display the specified setting.
     */
    public function show($key)
    {
        $value = setting($key);

        // Determine value type for display
        $valueType = 'string'; // default
        if (is_bool($value)) {
            $valueType = 'boolean';
        } elseif (is_numeric($value)) {
            $valueType = 'number';
        } elseif (is_array($value) || (is_string($value) && json_decode($value) !== null)) {
            $valueType = 'json';
        }

        return view('admin.settings.show', compact('key', 'value', 'valueType'));
    }

    /**
     * Show the form for editing the specified setting.
     */
    public function edit($key)
    {
        // First try to find the setting using new group structure
        $setting = DB::table('settings')->where('key', $key)->first();

        if (!$setting) {
            // Fallback: try old dot notation approach
            $value = Setting::get($key);
            if ($value === null) {
                abort(404, 'Setting not found');
            }

            // Split key into prefix and setting name for backward compatibility
            $prefix = '';
            $settingName = $key;
            $group = '';

            if (strpos($key, '.') !== false) {
                $parts = explode('.', $key, 2);
                $prefix = $parts[0];
                $settingName = $parts[1];
                $group = $parts[0];
            }
        } else {
            // New structure: use group field
            $value = $setting->value;
            $group = $setting->group;
            $settingName = $setting->key;
            $prefix = $setting->group; // For backward compatibility in views
            $key = $setting->key; // Use the actual key without group prefix
        }

        // Determine value type for form handling
        $valueType = 'string'; // default
        if (is_bool($value)) {
            $valueType = 'boolean';
        } elseif (is_numeric($value)) {
            $valueType = 'number';
        } elseif (is_array($value) || (is_string($value) && json_decode($value) !== null)) {
            $valueType = 'json';
        }

        // Optional description (could be added to database later)
        $description = '';

        return view('admin.settings.edit', compact('key', 'value', 'prefix', 'settingName', 'group', 'valueType', 'description'));
    }

    /**
     * Update the specified setting.
     */
    public function update(Request $request, $key)
    {
        $request->validate([
            'value' => 'required',
        ]);

        // Check if this is a new group-based setting
        $setting = DB::table('settings')->where('key', $key)->first();

        if ($setting) {
            // Update using new structure
            DB::table('settings')
                ->where('key', $key)
                ->update(['value' => $request->value]);
        } else {
            // Fallback to old method for backward compatibility
            Setting::set($key, $request->value);
        }

        return redirect()->route('admin.settings.index')
                        ->with('success', 'Setting updated successfully.');
    }

    /**
     * Remove the specified setting.
     */
    public function destroy($key)
    {
        // Check if this is a new group-based setting
        $setting = DB::table('settings')->where('key', $key)->first();

        if ($setting) {
            // Delete using new structure
            DB::table('settings')->where('key', $key)->delete();
        } else {
            // Fallback to old method for backward compatibility
            Setting::forget($key);
        }

        return redirect()->route('admin.settings.index')
                        ->with('success', 'Setting deleted successfully.');
    }

    /**
     * AdminLTE specific settings management
     */
    public function adminlte()
    {
        // Create SettingHelper instance with AdminLTE prefix
        $settingHelper = new SettingHelper('adminlte');

        // Get all AdminLTE settings
        $adminlteSettings = $settingHelper->all();
        $groupedSettings = $settingHelper->getGrouped();

        // Debug the layout_dark_mode value being passed to the view
        Log::info('AdminLTE View Debug', [
            'layout_dark_mode_value' => $adminlteSettings['layout_dark_mode'] ?? 'NOT_SET',
            'layout_dark_mode_type' => gettype($adminlteSettings['layout_dark_mode'] ?? null),
            'all_settings_count' => count($adminlteSettings),
            'first_10_settings' => array_slice($adminlteSettings, 0, 10, true)
        ]);

        return view('admin.settings.adminlte', compact('adminlteSettings', 'groupedSettings'));
    }

    /**
     * Update AdminLTE settings
     */
    public function updateAdminlte(Request $request)
    {
        // Debug logging
        Log::info('AdminLTE Settings Update Request', [
            'request_data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->url(),
            'timestamp' => now()
        ]);

        $settingHelper = new SettingHelper('adminlte');

        $settings = $request->except(['_token', '_method', 'current_tab']);

        // Special debug for dark mode setting
        Log::info('DARK MODE DEBUG - Raw request data', [
            'all_request_data' => $request->all(),
            'layout_dark_mode_in_request' => $request->has('layout_dark_mode'),
            'layout_dark_mode_value' => $request->get('layout_dark_mode'),
            'layout_dark_mode_in_settings' => isset($settings['layout_dark_mode']),
            'settings_dark_mode_value' => $settings['layout_dark_mode'] ?? 'NOT_SET',
            'current_tab' => $request->get('current_tab', 'NOT_SET')
        ]);

        Log::info('AdminLTE Settings to be saved', [
            'settings_count' => count($settings),
            'sidebar_settings' => array_filter($settings, function($key) {
                return str_starts_with($key, 'sidebar_');
            }, ARRAY_FILTER_USE_KEY),
            'all_settings' => $settings
        ]);

        foreach ($settings as $key => $value) {
            $oldValue = $settingHelper->get($key);

            // Special debug for layout_dark_mode
            if ($key === 'layout_dark_mode') {
                Log::info("DARK MODE DEBUG - Processing", [
                    'key' => $key,
                    'received_value' => $value,
                    'received_type' => gettype($value),
                    'old_database_value' => $oldValue,
                    'old_database_type' => gettype($oldValue),
                    'raw_request_value' => $request->get('layout_dark_mode', 'NOT_FOUND')
                ]);
            }

            $settingHelper->set($key, $value);

            // Verify the value was actually saved
            $verifyValue = $settingHelper->get($key);
            if ($key === 'layout_dark_mode') {
                Log::info("DARK MODE DEBUG - After save", [
                    'key' => $key,
                    'saved_value' => $value,
                    'verified_value' => $verifyValue,
                    'save_successful' => ($value === $verifyValue)
                ]);
            }

            // Log individual setting changes
            if ($oldValue !== $value) {
                Log::info("Setting changed: {$key}", [
                    'old_value' => $oldValue,
                    'new_value' => $value,
                    'type_old' => gettype($oldValue),
                    'type_new' => gettype($value)
                ]);
            }
        }

        // Handle tab state restoration
        $redirectUrl = route('admin.settings.adminlte');
        if ($request->has('current_tab') && $request->current_tab) {
            $redirectUrl .= $request->current_tab;
        }

        Log::info('AdminLTE Settings Update Complete', [
            'updated_count' => count($settings),
            'redirect_url' => $redirectUrl,
            'tab' => $request->current_tab
        ]);

        return redirect($redirectUrl)
                        ->with('success', 'AdminLTE settings updated successfully.')
                        ->with('active_tab', $request->current_tab);
    }

    /**
     * Debug AdminLTE settings - check database vs config
     */
    public function debugAdminlte()
    {
        $settingHelper = new SettingHelper('adminlte');

        // Get database settings
        $databaseSettings = $settingHelper->all();

        // Get config settings
        $configSettings = config('adminlte');

        // Get raw database entries to check what's actually stored
        $rawSettings = DB::table('settings')
            ->where('key', 'like', 'adminlte.%')
            ->get()
            ->pluck('value', 'key')
            ->mapWithKeys(function ($value, $key) {
                return [str_replace('adminlte.', '', $key) => $value];
            });

        return response()->json([
            'database_count' => count($databaseSettings),
            'config_count' => count($configSettings),
            'raw_count' => count($rawSettings),
            'database_settings' => array_slice($databaseSettings, 0, 10, true),
            'config_settings' => array_slice($configSettings, 0, 10, true),
            'raw_settings' => array_slice($rawSettings->toArray(), 0, 10, true),
            'sidebar_settings' => array_filter($databaseSettings, function($key) {
                return str_starts_with($key, 'sidebar_');
            }, ARRAY_FILTER_USE_KEY),
            'last_updated' => now()->toDateTimeString()
        ]);
    }

    /**
     * Test the settings system
     */
    public function test()
    {
        // Test basic functionality
        $tests = [];

        // Test 1: Basic setting without prefix
        Setting::set('test_setting', 'test_value');
        $tests['basic_set'] = Setting::get('test_setting') === 'test_value';

        // Test 2: Setting with SettingHelper and prefix
        $settingHelper = new SettingHelper('test');
        $settingHelper->set('helper_setting', 'helper_value');
        $tests['helper_set'] = $settingHelper->get('helper_setting') === 'helper_value';

        // Test 3: Verify prefix is working
        $tests['prefix_working'] = Setting::get('test.helper_setting') === 'helper_value';

        // Test 4: Get all settings
        $allSettings = Setting::all();
        $tests['all_settings_count'] = count($allSettings) > 0;

        // Clean up test settings
        Setting::forget('test_setting');
        Setting::forget('test.helper_setting');

        return view('admin.settings.test', compact('tests', 'allSettings'));
    }

    /**
     * Display storage configuration settings
     */
    public function storage()
    {
        $storageSettings = [
            'media_s3' => [
                'key' => env('MEDIA_S3_ACCESS_KEY', ''),
                'secret' => env('MEDIA_S3_SECRET_KEY', ''),
                'region' => env('MEDIA_S3_REGION', ''),
                'bucket' => env('MEDIA_S3_BUCKET', ''),
                'endpoint' => env('MEDIA_S3_ENDPOINT', ''),
            ],
            'aws' => [
                'key' => env('AWS_ACCESS_KEY_ID', ''),
                'secret' => env('AWS_SECRET_ACCESS_KEY', ''),
                'region' => env('AWS_DEFAULT_REGION', ''),
                'bucket' => env('AWS_BUCKET', ''),
                'endpoint' => env('AWS_ENDPOINT', ''),
            ]
        ];

        return view('admin.settings.storage', compact('storageSettings'));
    }

    /**
     * Update storage configuration settings
     */
    public function updateStorage(Request $request)
    {
        $request->validate([
            'media_s3_key' => 'nullable|string',
            'media_s3_secret' => 'nullable|string',
            'media_s3_region' => 'nullable|string',
            'media_s3_bucket' => 'nullable|string',
            'media_s3_endpoint' => 'nullable|url',
        ]);

        // Store S3 media settings
        Setting::set('storage.media_s3_key', $request->input('media_s3_key'));
        Setting::set('storage.media_s3_secret', $request->input('media_s3_secret'));
        Setting::set('storage.media_s3_region', $request->input('media_s3_region'));
        Setting::set('storage.media_s3_bucket', $request->input('media_s3_bucket'));
        Setting::set('storage.media_s3_endpoint', $request->input('media_s3_endpoint'));

        return redirect()->route('admin.settings.storage')
            ->with('success', 'Storage settings updated successfully. Note: Environment variables take precedence over database settings.');
    }

    /**
     * Display authentication settings
     */
    public function auth()
    {
        $siteConfigService = app(SiteConfigService::class);
        $authSettings = $siteConfigService->getAuthSettings();
        $siteSettings = $siteConfigService->getSiteSettings();

        return view('admin.settings.auth', compact('authSettings', 'siteSettings'));
    }

    /**
     * Update authentication settings
     */
    public function updateAuth(Request $request)
    {
        $request->validate([
            'login_title' => 'nullable|string|max:255',
            'login_subtitle' => 'nullable|string|max:255',
            'password_reset_enabled' => 'nullable|boolean',
            'registration_enabled' => 'nullable|boolean',
            'remember_me_enabled' => 'nullable|boolean',
            'password_min_length' => 'required|integer|min:6|max:128',
            'password_require_uppercase' => 'nullable|boolean',
            'password_require_lowercase' => 'nullable|boolean',
            'password_require_numbers' => 'nullable|boolean',
            'password_require_symbols' => 'nullable|boolean',
        ]);

        $siteConfigService = app(SiteConfigService::class);

        // Update auth settings
        $siteConfigService->setAuthSetting('login_title', $request->input('login_title'));
        $siteConfigService->setAuthSetting('login_subtitle', $request->input('login_subtitle'));
        $siteConfigService->setAuthSetting('password_reset_enabled', (bool) $request->input('password_reset_enabled', false));
        $siteConfigService->setAuthSetting('registration_enabled', (bool) $request->input('registration_enabled', false));
        $siteConfigService->setAuthSetting('remember_me_enabled', (bool) $request->input('remember_me_enabled', false));
        $siteConfigService->setAuthSetting('password_min_length', (int) $request->input('password_min_length'));
        $siteConfigService->setAuthSetting('password_require_uppercase', (bool) $request->input('password_require_uppercase', false));
        $siteConfigService->setAuthSetting('password_require_lowercase', (bool) $request->input('password_require_lowercase', false));
        $siteConfigService->setAuthSetting('password_require_numbers', (bool) $request->input('password_require_numbers', false));
        $siteConfigService->setAuthSetting('password_require_symbols', (bool) $request->input('password_require_symbols', false));

        return redirect()->route('admin.settings.auth')
            ->with('success', 'Authentication settings updated successfully.');
    }
}
