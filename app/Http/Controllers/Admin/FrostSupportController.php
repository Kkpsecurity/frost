<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FrostSupportController extends Controller
{
    /**
     * Display the Support SPA dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth('admin')->user();
        $isAdmin = $user->hasRole('admin');
        $isSysAdmin = $user->hasRole('sys-admin');

        return view('admin.frost-support.index', [
            'isAdmin' => $isAdmin,
            'isSysAdmin' => $isSysAdmin
        ]);
    }
}
