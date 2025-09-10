<?php
namespace App\Http\Controllers\React;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Traits\PageMetaDataTrait;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator as Validate;

class AccountUserController extends Controller
{
    use PageMetaDataTrait;

    /**
     * Get Data
     */
    public function getUserById($user_id)
    {

        $user = User::find($user_id);

        if (!$user) {
            $data['success'] = false;
            $data['message'] = 'Invalid User Account!';
        } else {
            $data['success'] = true;
            $user->avatar = $user->getAvatar('thumb');
            $data['user'] = $user;
        }
        
        return response()->json($data);
    }

    /**
     * Update Profile
     */
    public function updateProfile(Request $request, $user_id)
    {

        $user = User::find($user_id);

        $validate = Validate::make($request->all(), [
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'dob' => ['required', 'date'],
            'phone' => ['required', 'string', 'max:25'],
        ]);

        if (!empty($validate->errors()->messages())) {
            $errors = $validate->errors()->messages();
            foreach ($errors as $field => $message) {
                $errno[] = $message[0];
            }

            $response['success'] = false;
            $response['message'] = $errno[0];

            return response()->json($response);
        }


        // if email changes make them revalidate the new email address
        if ($user->email !== $request->email) {
            $user->email_verified_at = null;
            $user->sendEmailVerificationNotification();
        }

        $user->fname = $request->fname;
        $user->lname = $request->lname;
        $user->email = $request->email;

        $user->student_info = [
            'fname' => $request->fname, 
            'initial' => $request->initial,
            'lname' => $request->lname,
            'suffix' => $request->suffix,
            'dob' => $request->dob,
            'phone' => $request->phone
        ];

        $user->save();

        $response['success'] = false;
        $response['data'] = $user;
        $response['message'] = 'Account Profile Updated Successfully!';

        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'password' => 'required|different:old_password|confirmed|hash:' . $user->password,
        ]);

        if ($validatedData->fails()) {
            $response['success'] = false;
            $response['message'] = $validatedData->errors()->first();

            return response()->json($response);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $response['success'] = true;
        $response['message'] = "Password has been updated successfully!";

        return response()->json($response);
    }


    /**
     * Note: The Request parent is being converted into an array by react
     * So we just by call the request as an array
     *
     * Upload Avatar
     */
    public function uploadAvatar(Request $request)
    {
        $user = User::find($request->user_id);
        $uploadfile = $request->file('avatar');

        if ($uploadfile) {
            $path = $uploadfile->store('public/avatars');
            if ($path) {
                $user->avatar = basename($path);
            } else {
                $response['success'] = false;
                $response['message'] = "Failed to upload Avatar!";

                return response()->json($response);
            }
        }

        $user->use_gravatar = $request->enable_gravatar;
        $user->save();

        $response['success'] = true;
        $response['message'] = "Avatar updated successfully!";

        return response()->json($response);
    }


    public function removeAvatar(Request $request)
    {
        $user = User::find($request->user_id);
        $avatar = $user->avatar;

        if (Storage::delete('public/avatars/' . $avatar)) {
            $user->avatar = null;
            $user->save();

            $response['success'] = true;
            $response['message'] = "Avatar removed successfully!";
        } else {
            $response['success'] = false;
            $response['message'] = "Failed to remove Avatar!";
        }

        return response()->json($response);
    }



    /**
     * @param Request $request
     * @return mixed
     */
    public function AccountUpdate(Request $request)
    {

        $profile = User::find(Auth()->id());

        // Do Validation
        $validation = \Validator::make($request->all(), [
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required|unique:users,email,' . $profile->id,
        ]);

        if ($validation->fails()) {
            $content['success'] = false;
            $content['message'] = $validation->errors()->first();
        } else {

            $profile->fname = $request->fname;
            $profile->lname = $request->lname;
            $profile->email = $request->email;
            $profile->updated_at = Carbon::now();

            $profile->save();

            $content['success'] = true;
            $content['message'] = 'Updated Successfully';
        }


        if ($request->ajax()) {
            return response()->json($content);
        } else {
            if ($content['success']) {
                flash($content['message'])->success();
            } else {
                flash($content['message'])->error();
            }
            return Redirect()->back();
        }

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function AccountAvatarUpdate(Request $request): mixed
    {

        $user = User::find(Auth()->user()->id);

        /**
         * Do the Upload
         */
        if ($request->hasFile('avatar') === TRUE) {

            $path = $request->file('avatar')->store('avatars', config('lfm.disk', 'local'));
            Storage::disk(config('lfm.disk', 'local'))->setVisibility($path, 'public');

            $avatar = [
                'filename' => basename($path),
                'url' => Storage::disk(config('lfm.disk', 'local'))->url($path)
            ];

            $user->avatar = json_encode($avatar);
        }

        /* if($request->input('use_gravatar') === "on") {
             $user->use_gravatar  = TRUE;
         } else {
             $user->use_gravatar  = FALSE;
         }*/

        $user->save();

        $response['success'] = true;
        $response['message'] = 'You have updated your avatar';

        return Redirect('admin/account/dashboard');
        //  return response()->json($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function AccountAvatarDelete(Request $request): mixed
    {
        $user = Auth()->user();

        $avatar = json_decode($user->avatar);

        Storage::disk(config('lfm.disk', 'local'))->delete('avatars/' . $avatar->filename);
        $user->avatar = NULL;
        $user->save();

        $response['success'] = true;
        $response['message'] = 'You have updated your avatar';

        return response()->json($response);
    }