<?php

namespace App\Http\Controllers\Admin\Temp;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;

use App\Traits\PageMetaDataTrait;

use App\RCache;
use App\Models\SiteConfig;


class SiteConfigController extends Controller
{

    use PageMetaDataTrait;



    public function index()
    {

        $view    = 'admin.temp.site_configs';
        $widgets = scandir(resource_path('views/admin/plugins/widgets/dashboard'));
        $content = array_merge([
            'widgets' => $widgets,
        ], self::renderPageMeta( $view ));

        $SiteConfigs = RCache::SiteConfigs();

        return view( $view, compact( 'content', 'SiteConfigs' ) );

    }


    public function Create( Request $Request )
    {

        $SiteConfig = SiteConfig::create([
            'cast_to'       => $Request->input( 'cast_to' ),
            'config_name'   => $Request->input( 'config_name' ),
            'config_value'  => $Request->input( 'config_value' ),
        ]);

        return back()->with( 'success', "Added {$SiteConfig->config_name}" );

    }


    public function Update( Request $Request, SiteConfig $SiteConfig )
    {

        $SiteConfig->config_value = $Request->input( 'admin_config_value' );
        $SiteConfig->save();

        return back()->with( 'success', "Updated {$SiteConfig->config_name}" );

    }


}
