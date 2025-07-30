<?php

namespace App\Http\Controllers\Admin\AdminCenter;

/**
 * SettingsController
 * Handles CRUD operations for admin settings
 */

use Illuminate\Http\Request;
use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use Akaunting\Setting\Facade as Setting;

class SettingsController extends Controller
{
    /**
     * Display a listing of the settings.
     */
    public function index()
    {
        // Get all settings grouped by prefix
        $allSettings = Setting::all();
        $groupedSettings = [];

        foreach ($allSettings as $key => $value) {
            if (strpos($key, '.') !== false) {
                $parts = explode('.', $key, 2);
                $prefix = $parts[0];
                $setting = $parts[1];
                $groupedSettings[$prefix][$setting] = $value;
            } else {
                $groupedSettings['general'][$key] = $value;
            }
        }

        return view('admin.admin-center.settings.index', compact('groupedSettings'));
    }

    /**
     * Show the form for creating a new setting.
     */
    public function create()
    {
        return view('admin.admin-center.settings.create');
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
        return view('admin.admin-center.settings.show', compact('key', 'value'));
    }

    /**
     * Show the form for editing the specified setting.
     */
    public function edit($key)
    {
        $value = Setting::get($key);

        if ($value === null) {
            abort(404, 'Setting not found');
        }

        // Split key into prefix and setting name
        $prefix = '';
        $settingName = $key;

        if (strpos($key, '.') !== false) {
            $parts = explode('.', $key, 2);
            $prefix = $parts[0];
            $settingName = $parts[1];
        }

        return view('admin.admin-center.settings.edit', compact('key', 'value', 'prefix', 'settingName'));
    }

    /**
     * Update the specified setting.
     */
    public function update(Request $request, $key)
    {
        $request->validate([
            'value' => 'required',
        ]);

        Setting::set($key, $request->value);

        return redirect()->route('admin.settings.index')
                        ->with('success', 'Setting updated successfully.');
    }

    /**
     * Remove the specified setting.
     */
    public function destroy($key)
    {
        Setting::forget($key);

        return redirect()->route('admin.settings.index')
                        ->with('success', 'Setting deleted successfully.');
    }

    /**
     * AdminLTE specific settings management
     */
    public function adminlte()
    {
        // Set prefix for AdminLTE settings
        SettingHelper::setPrefix('adminlte');

        // Get all AdminLTE settings
        $adminlteSettings = SettingHelper::all();
        $groupedSettings = SettingHelper::getGrouped();

        return view('admin.admin-center.settings.adminlte', compact('adminlteSettings', 'groupedSettings'));
    }

    /**
     * Update AdminLTE settings
     */
    public function updateAdminlte(Request $request)
    {
        SettingHelper::setPrefix('adminlte');

        $settings = $request->except(['_token', '_method']);

        foreach ($settings as $key => $value) {
            SettingHelper::set($key, $value);
        }

        return redirect()->route('admin.settings.adminlte')
                        ->with('success', 'AdminLTE settings updated successfully.');
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
        SettingHelper::setPrefix('test');
        SettingHelper::set('helper_setting', 'helper_value');
        $tests['helper_set'] = SettingHelper::get('helper_setting') === 'helper_value';

        // Test 3: Verify prefix is working
        $tests['prefix_working'] = Setting::get('test.helper_setting') === 'helper_value';

        // Test 4: Get all settings
        $allSettings = Setting::all();
        $tests['all_settings_count'] = count($allSettings) > 0;

        // Clean up test settings
        Setting::forget('test_setting');
        Setting::forget('test.helper_setting');

        return view('admin.admin-center.settings.test', compact('tests', 'allSettings'));
    }
}
