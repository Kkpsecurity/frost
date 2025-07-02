<?php 
namespace App\Http\Controllers\Web\Blog;

use App\Http\Controllers\Controller;
use App\Traits\PageMetaDataTrait;

class BlogController extends Controller
{

    use PageMetaDataTrait;
    
    public function details($slug)
    {

        $content = array_merge([], self::renderPageMeta($slug));

        return view('frontend.blog.details', compact('content', 'slug'));
    }
}