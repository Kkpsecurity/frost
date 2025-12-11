<?php

namespace App\Http\Controllers\Web;

use App\Traits\PageMetaDataTrait;
use App\Http\Controllers\Controller; // Added the missing Controller import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
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
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|max:20',
            'subject' => 'nullable|max:255',
            'message' => 'required|max:2000',
            'privacy_agree' => 'required|accepted'
        ], [
            'privacy_agree.required' => 'You must agree to the Privacy Policy to submit this form.',
            'privacy_agree.accepted' => 'You must agree to the Privacy Policy to submit this form.'
        ]);

        try {
            // Use site settings for email recipient
            $recipientEmail = setting('contact_email', config('mail.from.address', 'contact@example.com'));

            Mail::to($recipientEmail)->send(new ContactForm($data));

            // Redirect back with success message
            return redirect()->back()->with('success', 'Thank you for your message! We will get back to you soon.');

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Contact form submission failed: ' . $e->getMessage());

            // Redirect back with error message
            return redirect()->back()
                ->withInput()
                ->with('error', 'Sorry, there was an issue sending your message. Please try again or contact us directly.');
        }
    }
}
