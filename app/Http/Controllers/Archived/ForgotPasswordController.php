<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\PageMetaDataTrait;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails, PageMetaDataTrait;

    public function showLinkRequestForm()
    {
        $content = array_merge([], self::renderPageMeta("Password Reset"));

        return view('frontend.auth.passwords.email', compact('content'));
    }

    
}