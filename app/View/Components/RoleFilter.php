<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Support\RoleManager;

class RoleFilter extends Component
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        $roleOptions = RoleManager::getAdminRoleOptions();

        return view('components.role-filter', compact('roleOptions'));
    }
}
