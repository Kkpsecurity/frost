<?php

namespace App\View\Components\Admin\Datatables;

use Illuminate\View\Component;

class AdminDatatable extends Component
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.admin.datatables.admin-datatable');
    }
}
