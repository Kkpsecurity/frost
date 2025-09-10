<?php namespace App\Http\Controllers\Auth;

use App\Models\User;
use Auth;
use Illuminate\Http\Request; 
use App\Traits\PageMetaDataTrait;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{

    use PageMetaDataTrait;

    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function showResetForm(Request $request, $token = null)
    {
        $content = array_merge([], self::renderPageMeta('reset-password'));
        return view('frontend.auth.passwords.reset', compact('content', 'token'));
    }

    public function reset(Request $request)
    {
        // Validate the new password
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8'],
            'token' => ['required', 'string'],
        ]);

        // Get the user by their email address
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No user found with this email address']);
        }

        // Verify if the reset password token is valid
        $tokenData = \DB::table('password_resets')->where('email', $request->email)->first();

        if (!$tokenData || !Hash::check($request->token, $tokenData->token)) {
            return back()->withErrors(['error' => 'Invalid or expired password reset token you must crate a new reset session']);
        }

        // Hash and update the new password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the token
        \DB::table('password_resets')->where('email', $request->email)->delete();

        // Redirect the user back with a success message
        return redirect('login')->with('success', 'Password updated successfully');
    }


}