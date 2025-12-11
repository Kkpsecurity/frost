<?php

namespace App\View\Components\Admin\Datatables\Partials;

use Illuminate\View\Component;

class AdminTableCrudBar extends Component
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.admin.datatables.partials.admin-table-crud-bar');
    }
}
