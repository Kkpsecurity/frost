<?php

namespace App\Http\Controllers\Admin\AdminCenter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MediaManagerController extends Controller
{
    /**
     * Display the media manager interface
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.admin-center.media.index');
    }
}
