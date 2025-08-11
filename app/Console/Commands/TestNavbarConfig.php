<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\SettingHelper;
use App\Support\AdminLteConfigElements;

class TestNavbarConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-navbar-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test AdminLTE navbar configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing AdminLTE Navbar Configuration...');

        try {
            $settingHelper = new SettingHelper();
            $configElements = new AdminLteConfigElements($settingHelper);
            $menuConfigResult = $configElements->getMenuConfig();

            $this->info('Menu config structure:');
            $this->line('Keys: ' . implode(', ', array_keys($menuConfigResult)));

            // Get the actual menu items
            $menuItems = $menuConfigResult['menu'] ?? [];
            $this->info('Total menu items: ' . count($menuItems));

            // Show first few items to debug
            $this->line('First 5 menu items:');
            foreach (array_slice($menuItems, 0, 5, true) as $key => $item) {
                $this->line("  Item $key:");
                $this->line("    Type: " . ($item['type'] ?? 'unknown'));
                $this->line("    topnav_right: " . (isset($item['topnav_right']) ? ($item['topnav_right'] ? 'true' : 'false') : 'not set'));
                if (isset($item['icon'])) {
                    $this->line("    Icon: " . $item['icon']);
                }
                if (isset($item['id'])) {
                    $this->line("    ID: " . $item['id']);
                }
                $this->line('');
            }

            $navbarItems = array_filter($menuItems, function($item) {
                return isset($item['topnav_right']) && $item['topnav_right'] === true;
            });

            $this->info('Found ' . count($navbarItems) . ' navbar items with topnav_right=true:');

            foreach ($navbarItems as $key => $item) {
                $this->line("  Navbar Item $key:");
                $this->line("    Type: " . ($item['type'] ?? 'unknown'));
                $this->line("    Icon: " . ($item['icon'] ?? 'no icon'));
                if (isset($item['id'])) {
                    $this->line("    ID: " . $item['id']);
                }
                $this->line('');
            }

            $this->info('Configuration test completed successfully!');

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
