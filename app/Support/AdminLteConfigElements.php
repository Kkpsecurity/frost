<?php

namespace App\Support;

use App\Helpers\SettingHelper;

class AdminLteConfigElements {

    protected $settingHelper;

    public function __construct(SettingHelper $settingHelper)
    {
        // Initialize any necessary properties or dependencies
        $this->settingHelper = $settingHelper;
        $this->settingHelper->setPrefix('adminlte');
    }

    // Add class members here as needed
    public function getTitle() {
        return ['title' => $this->settingHelper->get('title', 'Admin Panel')];
    }

    public function getTitlePrefix() {
        return ['title_prefix' => $this->settingHelper->get('title_prefix', '')];
    }

    public function getTitlePostfix() {
        return ['title_postfix' => $this->settingHelper->get('title_postfix', '')];
    }

}
