<?php namespace App\Http\Controllers\Admin\Services;

use Arr;
use File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;
use App\Traits\PageMetaDataTrait;


class AdminSearchController extends Controller
{

    use PageMetaDataTrait;

    /**
     * @param Request $request
     * @param string $action
     */
   public function search(Request $request, string $action='full')
   {
        $result = [];

        if($action == 'full') {
            $result = \App\Services\SiteSearchService::FullSiteSearch($request);
        }

       $content = array_merge([

       ], self::renderPageMeta('admin_dashboard'));

       return view('admin.search_results', compact('content', 'result'));
    }



}
