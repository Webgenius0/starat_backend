<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Helper\Helper;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use apiresponse;

    /**
     * Update user primary info
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateUserInfo(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . Auth::id()],
            'language' => ['required', 'string', 'in:en,ar'],
            'city' => ['nullable', 'string', 'max:50'],
            'state' => ['nullable', 'string', 'max:50'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'age' => ['nullable', 'integer', 'min:14'],
            'phone' => ['nullable', 'string'],
            'bio' => ['nullable', 'string'],
        ]);

        if ($validation->fails()) {
            return $this->error([], $validation->errors(), 500);
        }

        DB::beginTransaction();
        $user = Auth::user();
        try {
            $user = Auth::user();

            $user->update($request->only([
                    'username',
                    'email',
                    'password',
                    'language',
                    'city',
                    'state',
                    'age',
                    'phone',
                    'bio',
                ]));

            if ($request->hasFile('images')) {
                $userImages = [];
                foreach ($request->file('images') as $image) {
                    $url = Helper::fileUpload($image, 'users', $user->username . "-" . time());
                    array_push($userImages, [
                        'image' => $url
                    ]);
                }
                $user->images()->delete();
                $user->images()->createMany($userImages);
            }

            DB::commit();

            return $this->success([
                'user' => $user,
            ], 'User updated successfully', 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage(), 400);
        }
    }

    /**
     * Change Password
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'old_password' => 'required|string|max:255',
            'new_password' => 'required|string|max:255',
        ]);

        if ($validation->fails()) {
            return $this->error([], $validation->errors(), 500);
        }

        try
        {
            $user = User::where('id', Auth::id())->first();
            if (password_verify($request->old_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
                $user->save();
                return $this->success([], "Password changed successfully", 200);
            } else {
                return $this->error([], "Old password is incorrect", 500);
            }
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }



    /**
     * Get My Notifications
     * @return \Illuminate\Http\Response
     */
    public function getMyNotifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->get();
        return $this->success([
            'notifications' => $notifications,
        ], "Notifications fetched successfully", 200);
    }


    /**
     * Delete User
     * @return \Illuminate\Http\Response
     */
    public function deleteUser()
    {
        $user = User::where('id', Auth::id())->first();
        if ($user) {
            $user->delete();
            return $this->success([], "User deleted successfully", 200);
        } else {
            return $this->error("User not found", 404);
        }

    }
}
