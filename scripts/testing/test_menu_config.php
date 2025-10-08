<?php

echo "=== ADMIN CENTER MENU TEST ===\n";

$config = include __DIR__ . '/../../config/adminlte_config.php';

$found = false;
foreach ($config['menu'] as $item) {
    if (isset($item['text']) && $item['text'] === 'Admin Center') {
        $found = true;
        echo "✅ Admin Center menu found\n";
        echo "Route: " . ($item['route'] ?? 'NOT SET') . "\n";
        echo "Icon: " . ($item['icon'] ?? 'NOT SET') . "\n";
        
        if (isset($item['submenu'])) {
            echo "❌ ERROR: Still has submenu with " . count($item['submenu']) . " items!\n";
            foreach ($item['submenu'] as $sub) {
                echo "  - " . ($sub['text'] ?? 'No text') . "\n";
            }
        } else {
            echo "✅ SUCCESS: No submenu - single dashboard link\n";
        }
        break;
    }
}

if (!$found) {
    echo "❌ Admin Center menu item not found!\n";
}

echo "=== TEST COMPLETE ===\n";