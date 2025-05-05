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
            'name' => ['sometimes', 'string', 'max:255'],
            'username' => ['sometimes', 'string', 'max:255', 'unique:users,username,' . Auth::id()],
            'location' => ['sometimes', 'nullable', 'string', 'max:50'],
            'website' => ['sometimes', 'nullable', 'string', 'max:50'],
            'avatar' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'cover_image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'phone' => ['sometimes', 'nullable', 'string'],
            'bio' => ['sometimes', 'nullable', 'string'],
        ]);

        if ($validation->fails()) {
            return $this->error([], $validation->errors(), 422);
        }

        DB::beginTransaction();
        $user = Auth::user();

        try {
            // Only update the fields that are actually sent in the request
            $user->update($request->only([
                'name',
                'username',
                'location',
                'website',
                'phone',
                'bio',
            ]));

            if ($request->hasFile('avatar')) {
                $filePath = Helper::uploadImage($request->file('avatar'), 'users', $user->avatar ?? null);
                $user->avatar = $filePath;
            }

            if ($request->hasFile('cover_image')) {
                $coverImagePath = Helper::uploadImage($request->file('cover_image'), 'users/covers', $user->cover_image);
                $user->cover_image = $coverImagePath;
            }

            $user->save();
            DB::commit();

            return $this->success([
                'user' => [
                    'name' => $user->name,
                    'username' => $user->username,
                    'location' => $user->location,
                    'website' => $user->website,
                    'avatar' => $user->avatar,
                    'cover_image' => $user->cover_image,
                    'phone' => $user->phone,
                    'bio' => $user->bio,
                ],
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

        try {
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
