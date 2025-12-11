<?php namespace App\Http\Controllers\Admin\Account;


use Auth;
use KKP\pgTk;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\PageMetaDataTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AccountDashboardController extends Controller
{


    use PageMetaDataTrait;

    /**
     * @return mixed
     */
    public function dashboard(): mixed
    {

        $content = array_merge([ ], self::renderPageMeta('admin_dashboard'));

        return view('admin.account.dashboard', compact('content'));
    }

    /**
     * @param Request $request
     */
    public function updateProfile (Request $request)
    {
        $user = Auth::user();

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required|unique:users,email,' . $user->id,
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

        // Update the user profile
        $user->fname = $request->input('fname');
        $user->lname = $request->input('lname');
        $user->email = $request->input('email');
        $user->updated_at = pgTk()->now();
        $user->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Updated successfully',
            ]);
        } else {
            flash('Updated successfully')->success();
            return redirect()->back();
        }
    }
   
    /**
     * Update the user's avatar.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function accountAvatarUpdate(Request $request)
    {
        $user = Auth::user();

        if ($request->isMethod('post')) {
            // Do the upload
            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('avatars', config('lfm.disk', 'local'));
                Storage::disk(config('lfm.disk', 'local'))->setVisibility($path, 'public');

                $avatar = [
                    'filename' => basename($path),
                    'url' => Storage::disk(config('lfm.disk', 'local'))->url($path),
                ];

                $user->avatar = json_encode($avatar);
            }

            if ($request->input('use_gravatar') === "on") {
                $user->use_gravatar = true;
            } else {
                $user->use_gravatar = false;
            }

            $user->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'You have updated your avatar.',
                ]);
            }

            $request->session()->flash('success', 'You have updated your avatar.');
        }

        return redirect()->route('admin.account.dashboard');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function accountAvatarDelete(Request $request): mixed
    {
        $user = Auth::user();

        $avatar = json_decode($user->avatar);

        if ($avatar) {
            Storage::disk(config('lfm.disk', 'local'))->delete('avatars/' . $avatar->filename);
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

        return redirect()->route('admin.account.dashboard');
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
            'current_password' => 'required|password',
            'new_password' => 'required|min:8|confirmed',
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

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'You have updated your password.',
            ]);
        } else {
            flash('You have updated your password.')->success();
            return redirect()->back();
        }
    }



}
