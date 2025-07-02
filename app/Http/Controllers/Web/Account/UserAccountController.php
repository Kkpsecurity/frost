<?php

namespace App\Http\Controllers\Web\Account;

use App\Http\Controllers\Controller;
use App\Traits\PageMetaDataTrait;
use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use KKP\Laravel\PgTk;
use Storage;
use Validator;

class UserAccountController extends Controller
{

    use PageMetaDataTrait;

    /**
     * @return mixed
     */
    public function dashboard($page = 'dashboard')
    {
        $content = array_merge([
            'page' => $page,
            'user' => auth()->user(),
        ], self::renderPageMeta(('account_detail')));

        return view('frontend.account.dashboard', compact('content'));
    }


    public function details()
    {
        $content = array_merge([
            'user' => auth()->user(),
        ], self::renderPageMeta(('accoount_detail')));

        return view('frontend.account.detail', compact('content'));
    }

    /**
     * @param Request $request
     */
    public function updateProfile(Request $request)
{
    $user = Auth::user();

    // Validate the request data
    $validator = Validator::make($request->all(), [
        'fname' => 'required',
        'lname' => 'required',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'dob' => 'required|date',
        'phone' => 'required',            
    ]);

    if ($validator->fails()) {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        } else {
            flash($validator->errors()->first())->error();
            return redirect()->back();
        }
    }

    try {
        // Update the user profile within a transaction for safety
        DB::beginTransaction();

        $user->fname = $request->input('fname');
        $user->lname = $request->input('lname');
        $user->email = $request->input('email');

        // Assuming student_info is stored as a JSON column in your users table
        $user->student_info = json_encode([
            'initials' => $request->input('initials'),
            'dob' => $request->input('dob'),
            'suffix' => $request->input('suffix'),
            'phone' => $request->input('phone'),
        ]);

        $user->updated_at = now(); // Use now() helper for the current timestamp
        $user->save();

        DB::commit(); // Commit the transaction if no errors occurred

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
            ]);
        } else {
            flash('Profile updated successfully')->success();
            return redirect()->back();
        }
    } catch (\Exception $e) {
        DB::rollBack(); // Rollback the transaction on error

        // Handle the error
        $errorMessage = "An error occurred while updating the profile: " . $e->getMessage();
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ]);
        } else {
            flash($errorMessage)->error();
            return redirect()->back();
        }
    }
}


    /**
     * Update the user's password.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|password',
            'password' => 'required|min:8|confirmed',
        ]);

        $validator = Validator::make($request->all(), [
            'old_password' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('The old password is incorrect.');
                    }
                }
            ],
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ]);
            } else {
                flash($validator->errors()->first())->error();
                return redirect()->back();
            }
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'You have updated your password.',
            ]);
        } else {
            flash('You have updated your password.')->success();
            return redirect()->route('account', 'password');
        }
    }

    /**
     * Update the user's avatar.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function uploadAvatar(Request $request)
    {
        try {
            // Begin transaction
            \DB::beginTransaction();

            $user = Auth::user();

            // Do the upload
            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('public/avatars', config('filesystem.default', 'local'));

                if (config('filesystem.default', 'local') == 's3') {
                    Storage::disk(config('filesystem.default', 'local'))->setVisibility($path, 'public');
                }

                $avatar = [
                    'filename' => basename($path),
                    'url' => Storage::disk(config('filesystem.default', 'local'))->url($path),
                ];

                $user->avatar = json_encode($avatar);
            }

            $user->save();

            // If everything goes well, commit the transaction
            \DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'You have updated your avatar.',
                ]);
            }

            $request->session()->flash('success', 'You have updated your avatar.');

            return redirect()->route('account', 'avatar');

        } catch (\Exception $e) {
            // An error occurred; cancel the transaction...
            \DB::rollBack();

            // and return an error message...
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'There was an error updating your avatar.',
                ]);
            }

            $request->session()->flash('error', 'There was an error updating your avatar.');

            return redirect()->route('account', 'avatar');
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function removeAvatar(Request $request): mixed
    {
        $user = Auth::user();

        $avatar = json_decode($user->avatar);

        if ($avatar) {
            Storage::disk(config('filesystem.default', 'local'))->delete('avatars/' . $avatar->filename);
            $user->avatar = null;
            $user->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'You have updated your avatar',
                ]);
            }

            $request->session()->flash('success', 'You have updated your avatar.');
        }

        return redirect()->route('account', 'avatar');
    }

    /**
     * Update the user's Gravatar.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function updateGravatar(Request $request)
    {
        $user = Auth::user();

        // Toggle the value of use_gravatar.
        $user->use_gravatar = !$user->use_gravatar;
        $user->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'You have updated your Gravatar.',
            ]);
        } else {
            flash('You have updated your Gravatar.')->success();
            return redirect()->route('account', 'avatar');
        }
    }

    public function settings()
    {

        $content = array_merge([], self::renderPageMeta(('account_settings')));
        return view('frontend.account.settings', compact('content'));
    }
}