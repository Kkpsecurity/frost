<?php

namespace App\Http\Controllers\Web\Support;

use App\Http\Controllers\Controller;
use App\Traits\PageMetaDataTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SupportController extends Controller
{
    use PageMetaDataTrait;
        
    public function render(Request $request, $slug = 'dashboard')
    {
        // Add your logic here to handle different slugs or load content based on the slug
        
        // For example, if you have different views for different slugs:
        $viewName = 'frontend.support.' . $slug; // Assuming your view files are stored in the "resources/views/support" directory

        $content = array_merge([], self::renderPageMeta($slug));

        if (View::exists($viewName)) {
            return view($viewName, compact('content'));
        }

        // If the view for the slug doesn't exist, you can return a 404 page or handle it accordingly
        abort(404);
    }
}
