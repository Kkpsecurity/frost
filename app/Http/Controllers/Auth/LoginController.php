<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Traits\PageMetaDataTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

//
// added
//
use Auth;
use Illuminate\Http\Request;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, PageMetaDataTrait;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }


    /**
     * Show the application's login form.
     *
     * @return
     */
    public function showLoginForm()
    {
        $content = array_merge([], self::renderPageMeta("Student login"));

        return view('frontend.auth.login', compact('content'));
    }


    public function login(Request $request)
    {
        $rules = [
            'email'     => 'required|email',
            'password'  => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'There are validation errors',
                    'errors'    => $validator->errors(),
                ]);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login Successful',
                    'data' => [
                        'redirect_url' => Auth::user()->Dashboard(),
                    ],
                ]);
            } else {

                if ( Auth::user()->IsAnyAdmin() )
                {
                    return redirect( Auth::user()->Dashboard() );
                }

                return redirect()->intended()->withSuccess('Login Successfull');

            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Login details are not valid',
            ]);
        } else {
            return redirect("login")->withErrors(['email' => 'Login details are not valid']);
        }

    }

    protected function credentials(Request $Request)
    {
        return array_merge($Request->only($this->username(), 'password'), ['is_active' => 1]);
    }


    /**
     * @statutory compliance;
     * @desc: Student may only use one device at a time
     */
    protected function authenticated(Request $Request, $User)
    {

        //
        // in App/Http/Kernel.php:
        //   $middlewareGroups['web']
        //     \Illuminate\Session\Middleware\AuthenticateSession::class
        //

        if ($User->IsStudent()) {
            Auth::logoutOtherDevices($Request->input('password'));
        }

        return redirect()->intended();

    }


}
