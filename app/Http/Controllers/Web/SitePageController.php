<?php

namespace App\Http\Controllers\Web;

use App\Traits\PageMetaDataTrait;
use App\Http\Controllers\Controller; // Added the missing Controller import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactForm;

class SitePageController extends Controller
{
    use PageMetaDataTrait;

    /**
     * @param string|null $page
     * @return mixed
     */
    public function render(?string $page = 'home')
    {
        // Define pages with panel configurations - get all pages
        $pages = GetPageConfigurations();

        // Get the current page or default to home
        $currentPage = $page ?? 'home';

        // If page doesn't exist in our config, default to home
        if (!array_key_exists($currentPage, $pages)) {
            $currentPage = 'home';
        }

        $pageData = $pages[$currentPage];

        // Merge with meta data
        $content = array_merge($pageData, self::renderPageMeta($currentPage));

        return view('frontend.pages.render', compact('content', 'pages', 'currentPage'));
    }

    public function sendContactEmail(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
            'privacy_agree' => 'required|accepted'
        ], [
            'privacy_agree.required' => 'You must agree to the Privacy Policy to submit this form.',
            'privacy_agree.accepted' => 'You must agree to the Privacy Policy to submit this form.'
        ]);

        try {
            Mail::to('someemail@test.com')->send(new ContactForm($data));
        } catch (\Exception $e) {
            // Log the error or perform appropriate error handling
            return response()->json(['error' => 'Failed to send email'], 500);
        }

        return response()->json(['success' => true]);
    }
}
