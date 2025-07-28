<?php

declare(strict_types=1);

namespace App\Console\Commands;

/**
 * @file CreateSiteConfig.php
 * @brief Command to create a site configuration.
 * @details This command allows the creation of a site configuration with specified parameters.
 */

use Illuminate\Console\Command;

use App\Services\RCache;
use App\Models\SiteConfig;


class CreateSiteConfig extends Command
{

    protected $signature   = 'command:create_site_config {cast_to?} {config_name?} {config_value?}';
    protected $description = 'Create Site Config';


    public function handle(): int
    {

        $cast_to      = $this->_GetCastTo();
        $config_name  = $this->_GetConfigName();
        $config_value = $this->_GetConfigValue();


        $this->line("cast_to:      {$cast_to}");
        $this->line("config_name:  {$config_name}");
        $this->line("config_value: {$config_value}");

        if (! $this->confirm('Create SiteConfig?')) {
            return 1;
        }

        //
        //
        //

        $SiteConfig = SiteConfig::create([
            'cast_to'       => $cast_to,
            'config_name'   => $config_name,
            'config_value'  => $config_value
        ]);

        $this->info('Created SiteConfig');
        $this->line(print_r($SiteConfig->toArray(), true));


        return 0;
    }


    protected function _GetCastTo(): string
    {

        $casts = SiteConfig::Casts();
        unset($casts['htmltext']); // should not do this via command line
        $casts = array_keys($casts);

        //
        //
        //

        if ($cast_to = $this->argument('cast_to')) {

            if (in_array($cast_to, $casts)) {
                return $cast_to;
            }

            $this->error("Invalid cast_to '{$cast_to}'");
        }

        return $this->choice('Cast To', $casts, 'int');
    }


    protected function _GetConfigName(): string
    {

        $config_name = SiteConfig::SlugConfigName($this->argument('config_name'));

        do {

            if (! $config_name) {
                $config_name = SiteConfig::SlugConfigName($this->ask('config_name'));
            }

            if (RCache::SiteConfigs()->firstWhere('config_name', $config_name)) {
                $this->error("{$config_name} exists");
                $config_name = '';
            }
        } while (! $config_name);

        return $config_name;
    }


    protected function _GetConfigValue(): string
    {

        $config_value = $this->argument('config_value');

        while ($config_value == '') // allow zero
        {
            $config_value = $this->ask('config_value');
        }

        return $config_value;
    }
}
