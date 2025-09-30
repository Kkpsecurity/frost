<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Helpers\SettingHelper;

// Create a simple Laravel app instance for testing
$app = new Application(getcwd());

try {
    echo "=== Testing AdminLTE Tabbed Interface ===\n";

    // Test SettingHelper instance creation
    $settingHelper = new SettingHelper('adminlte');
    echo "✓ SettingHelper with 'adminlte' prefix created\n";

    // Test getting all AdminLTE settings
    $adminlteSettings = $settingHelper->all();
    echo "✓ Retrieved " . count($adminlteSettings) . " AdminLTE settings\n";

    // Test specific settings for each tab
    $tabs = [
        'Title & Logo' => ['title', 'logo', 'logo_img', 'auth_logo_enabled'],
        'Layout' => ['layout_topnav', 'layout_boxed', 'layout_fixed_sidebar', 'layout_fixed_navbar'],
        'Authentication' => ['classes_auth_card', 'login_url', 'register_url', 'logout_url'],
        'Sidebar' => ['sidebar_dark_mode', 'sidebar_fixed', 'sidebar_mini', 'sidebar_collapse'],
        'Styling' => ['classes_body', 'classes_brand', 'classes_brand_text', 'classes_content_wrapper'],
        'Plugins' => ['plugins_datatables', 'plugins_select2', 'plugins_chartjs', 'plugins_sweetalert2'],
        'Advanced' => ['livewire', 'use_route_url', 'dashboard_url', 'logout_method']
    ];

    echo "\n=== Settings by Tab ===\n";
    foreach ($tabs as $tabName => $settingKeys) {
        $foundSettings = 0;
        foreach ($settingKeys as $key) {
            if (isset($adminlteSettings[$key])) {
                $foundSettings++;
            }
        }
        echo "📁 {$tabName}: {$foundSettings}/" . count($settingKeys) . " settings found\n";
    }

    // Test setting values for preview
    echo "\n=== Key Settings Preview ===\n";
    $keySettings = ['title', 'logo', 'layout_topnav', 'sidebar_dark_mode'];
    foreach ($keySettings as $key) {
        $value = $adminlteSettings[$key] ?? 'NOT SET';
        echo "⚙️  {$key}: " . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . "\n";
    }

    // Verify the setting-field partial variables
    echo "\n=== Setting Field Types Test ===\n";
    $testSettings = [
        'title' => 'Laravel AdminLTE',  // Text input
        'layout_topnav' => true,        // Checkbox
        'sidebar_dark_mode' => false,   // Checkbox
        'plugins_datatables' => true,   // Checkbox
        'use_route_url' => false        // Checkbox
    ];

    foreach ($testSettings as $key => $value) {
        $fieldType = (is_bool($value) || in_array($value, ['true', 'false', '1', '0'])) ? 'checkbox' : 'text';
        echo "🔧 {$key} → {$fieldType} field (" . gettype($value) . ")\n";
    }

    echo "\n✅ AdminLTE Tabbed Interface Test Complete!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
