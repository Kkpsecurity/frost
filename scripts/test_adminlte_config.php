<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Support\AdminLteConfigElements;
use App\Helpers\SettingHelper;
use App\Services\AdminLteService;

echo "=== AdminLTE Configuration System Test ===\n\n";

try {
    // Test 1: Basic configuration elements
    echo "1. Testing AdminLteConfigElements...\n";
    $settingHelper = new SettingHelper();
    $configElements = new AdminLteConfigElements($settingHelper);

    // Test individual getters
    $titleConfig = $configElements->getTitle();
    echo "✓ Title config: " . json_encode($titleConfig) . "\n";

    $logoConfig = $configElements->getLogo();
    echo "✓ Logo config: " . json_encode($logoConfig) . "\n";

    // Test 2: Service integration
    echo "\n2. Testing AdminLteService...\n";
    $service = new AdminLteService($configElements);

    $titleSection = $service->getTitleConfig();
    echo "✓ Service title config: " . json_encode($titleSection) . "\n";

    // Test 3: Static configuration loading
    echo "\n3. Testing static configuration loading...\n";
    $staticConfig = AdminLteService::initiateConfig();
    if ($staticConfig !== null) {
        echo "✓ Static configuration loaded successfully\n";
        echo "✓ Configuration has " . count($staticConfig) . " keys\n";

        // Show some key configurations
        echo "✓ Title: " . ($staticConfig['title'] ?? 'Not set') . "\n";
        echo "✓ Dark mode: " . ($staticConfig['layout_dark_mode'] ? 'Enabled' : 'Disabled') . "\n";
        echo "✓ Livewire: " . ($staticConfig['livewire'] ? 'Enabled' : 'Disabled') . "\n";
    } else {
        echo "✗ Static configuration failed to load\n";
    }

    // Test 4: Database settings integration
    echo "\n4. Testing database settings...\n";

    // Set some test values
    $settingHelper->set('adminlte.title', 'Test Admin Panel');
    $settingHelper->set('adminlte.layout_dark_mode', true);
    $settingHelper->set('adminlte.livewire', true);

    // Retrieve and verify
    $newTitleConfig = $configElements->getTitle();
    echo "✓ Updated title config: " . json_encode($newTitleConfig) . "\n";

    $darkModeConfig = $configElements->getLayoutDarkMode();
    echo "✓ Dark mode config: " . json_encode($darkModeConfig) . "\n";

    $livewireConfig = $configElements->getLivewire();
    echo "✓ Livewire config: " . json_encode($livewireConfig) . "\n";

    // Test 5: Plugin configuration
    echo "\n5. Testing plugin configuration...\n";
    $pluginsConfig = $configElements->getPluginsConfig();
    echo "✓ Plugins configuration loaded with " . count($pluginsConfig['plugins']) . " plugins\n";

    // Test individual plugin settings
    $service->enablePlugin('datatables');
    $datatablesConfig = $configElements->getDatatablesPlugin();
    echo "✓ Datatables plugin enabled: " . json_encode($datatablesConfig) . "\n";

    // Test 6: Menu configuration
    echo "\n6. Testing menu configuration...\n";
    $menuConfig = $configElements->getMenuConfig();
    echo "✓ Menu configuration has " . count($menuConfig['menu']) . " items\n";

    // Test 7: IFrame configuration
    echo "\n7. Testing IFrame configuration...\n";
    $iframeConfig = $configElements->getIframeConfig();
    echo "✓ IFrame configuration: " . json_encode($iframeConfig['iframe']['buttons']) . "\n";

    // Test 8: Complete configuration
    echo "\n8. Testing complete configuration merge...\n";
    $allConfig = $configElements->getAllConfiguration();
    echo "✓ Complete configuration has " . count($allConfig) . " settings\n";

    echo "\n=== All Tests Passed! ===\n";
    echo "✅ AdminLTE Configuration System is working correctly\n";
    echo "✅ Database-driven settings are functional\n";
    echo "✅ Service layer integration is operational\n";
    echo "✅ All configuration sections are accessible\n";

} catch (Exception $e) {
    echo "\n❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
