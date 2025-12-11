<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Traits\PageMetaDataTrait;
use Illuminate\Foundation\Auth\VerifiesEmails;
use KKP\Laravel\PgTk;
use Mail;
use Request;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails, PageMetaDataTrait;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function show()
    {       
        $content = array_merge([], self::renderPageMeta('verify-email'));
        return view('frontend.auth.verify', compact('content'));
    }

    public function verify()
    {
        session()->forget('verificationEmailSent');

        $user = request()->user();
        $user->email_verified_at = PgTk::now();
        $user->save();

        $content = array_merge([], self::renderPageMeta('verify-success'));        
        return view('frontend.auth.verify_success', compact('content'));
    }

    public function resend()
    {
        session(['verificationEmailSent' => true]);
        $user = request()->user();
        $user->sendEmailVerificationNotification();

        return redirect()->back()->with('message', 'Email verification link sent!');
    }
}