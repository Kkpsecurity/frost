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
    public function render(string $page = null)
    {
        $content = array_merge([], self::renderPageMeta($page ?? 'index'));

        return view('frontend.pages.' . ($page ?? 'render'), compact('content'));
    }

    public function sendContactEmail(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required'
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
