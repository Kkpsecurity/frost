<?php

namespace App\Http\Controllers;

use App\Helpers\AdminLTEHelper;
use Illuminate\Http\Request;

class SettingsTestController extends Controller
{
    /**
     * Test page for AdminLTE Settings
     */
    public function index()
    {
        // Set the prefix for AdminLTE settings
        AdminLTEHelper::setPrefix('adminlte');

        // Test basic functionality
        $tests = [
            'Current prefix' => AdminLTEHelper::getPrefix(),
            'Example usage' => AdminLTEHelper::exampleUsage(),
        ];

        // Test setting and getting values
        AdminLTEHelper::set('title', 'Frost Admin Panel');
        AdminLTEHelper::set('layout.dark_mode', true);
        AdminLTEHelper::set('layout.sidebar_mini', 'lg');
        AdminLTEHelper::set('theme.primary_color', '#007bff');

        $tests['Set values'] = [
            'title' => 'Frost Admin Panel',
            'layout.dark_mode' => true,
            'layout.sidebar_mini' => 'lg',
            'theme.primary_color' => '#007bff',
        ];

        // Test getting values
        $tests['Retrieved values'] = [
            'title' => AdminLTEHelper::get('title'),
            'layout.dark_mode' => AdminLTEHelper::get('layout.dark_mode'),
            'layout.sidebar_mini' => AdminLTEHelper::get('layout.sidebar_mini'),
            'theme.primary_color' => AdminLTEHelper::get('theme.primary_color'),
            'non_existent' => AdminLTEHelper::get('non_existent', 'default_value'),
        ];

        // Test grouped settings
        $tests['Grouped settings'] = AdminLTEHelper::getGrouped();

        // Test all settings
        $tests['All settings'] = AdminLTEHelper::all();

        return view('admin.settings-test', compact('tests'));
    }

    /**
     * Clear all AdminLTE settings
     */
    public function clear()
    {
        AdminLTEHelper::setPrefix('adminlte');
        $cleared = AdminLTEHelper::clear();

        return response()->json([
            'success' => $cleared,
            'message' => $cleared ? 'All AdminLTE settings cleared!' : 'Failed to clear settings',
        ]);
    }

    /**
     * API endpoint to get setting
     */
    public function getSetting(Request $request)
    {
        AdminLTEHelper::setPrefix('adminlte');
        $key = $request->get('key');
        $default = $request->get('default', null);

        if (!$key) {
            return response()->json(['error' => 'Key is required'], 400);
        }

        $value = AdminLTEHelper::get($key, $default);

        return response()->json([
            'key' => $key,
            'value' => $value,
            'has_value' => AdminLTEHelper::has($key),
        ]);
    }

    /**
     * API endpoint to set setting
     */
    public function setSetting(Request $request)
    {
        AdminLTEHelper::setPrefix('adminlte');
        $key = $request->get('key');
        $value = $request->get('value');

        if (!$key) {
            return response()->json(['error' => 'Key is required'], 400);
        }

        $success = AdminLTEHelper::set($key, $value);

        return response()->json([
            'success' => $success,
            'key' => $key,
            'value' => $value,
            'message' => $success ? 'Setting saved!' : 'Failed to save setting',
        ]);
    }
}
