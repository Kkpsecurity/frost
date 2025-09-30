<?php

require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel properly
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Disabling Livewire in AdminLTE settings...\n";

try {
    // Disable livewire
    DB::table('settings')
        ->updateOrInsert(
            ['key' => 'adminlte.livewire'],
            ['value' => '0']
        );

    echo "✓ Livewire disabled\n";

    // Also disable any duplicate livewire settings
    DB::table('settings')
        ->where('key', 'adminlte_livewire')
        ->update(['value' => '0']);

    echo "✓ Duplicate livewire settings disabled\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Done! Refresh your browser.\n";
