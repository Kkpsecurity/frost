<?php
namespace App\Http\Controllers\Auth;


use App\Models\User;
use App\Providers\RouteServiceProvider;
use Auth;
use Illuminate\Http\Request;
use App\Mail\EmailVerification;
use App\Traits\PageMetaDataTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    use RegistersUsers, PageMetaDataTrait;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        $content = array_merge([], self::renderPageMeta("Student login"));

        return view('frontend.auth.register', compact('content'));
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'privacy_agree' => 'required|accepted'
        ]);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        // Log in the user
        Auth::login($user);

        $this->sendEmailVerification($user);

        return redirect()->route('pages');
    }

    protected function create(array $data)
    {
        return User::create([
            'fname' => $data['fname'],
            'lname' => $data['lname'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'remember_token' => \Illuminate\Support\Str::random(60),
        ]);
    }


    protected function sendEmailVerification(User $user)
    {
        $verificationUrl = route('verification.verify', [
            'id' => $user->id,
            'hash' => $user->remember_token,
        ]);

        Mail::to($user->email)->send(new EmailVerification($verificationUrl));
    }


}