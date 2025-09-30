<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

echo "=== Sidebar Menu Debug ===\n\n";

// Check menu configuration without authentication context
$menu = config('adminlte.menu');
echo "Menu items configured: " . (is_array($menu) ? count($menu) : 'NOT ARRAY') . "\n";

if (is_array($menu)) {
    echo "\nFirst few menu items:\n";
    foreach (array_slice($menu, 0, 5) as $i => $item) {
        echo "  " . ($i + 1) . ". " . ($item['text'] ?? 'NO TEXT') . "\n";
    }
}

// Check sidebar settings
echo "\nSidebar settings:\n";
echo "  sidebar_mini: " . config('adminlte.sidebar_mini', 'NOT SET') . "\n";
echo "  sidebar_collapse: " . (config('adminlte.sidebar_collapse', false) ? 'true' : 'false') . "\n";
echo "  sidebar_nav_accordion: " . (config('adminlte.sidebar_nav_accordion', true) ? 'true' : 'false') . "\n";
echo "  classes_sidebar: " . config('adminlte.classes_sidebar', 'NOT SET') . "\n";

echo "\nIf sidebar isn't showing, check if menu items have proper routes/URLs.\n";
