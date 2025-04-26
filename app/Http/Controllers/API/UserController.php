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
        // Validate the incoming request data
        $validation = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . Auth::id()],
            'location' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'string', 'max:50'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // Single profile image
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // Single cover image
            'phone' => ['nullable', 'string'],
            'bio' => ['nullable', 'string'],
        ]);

        // If validation fails, return errors
        if ($validation->fails()) {
            return $this->error([], $validation->errors(), 422);
        }

        // Start a database transaction to ensure atomicity
        DB::beginTransaction();
        $user = Auth::user();

        try {
            // Update the user's general information (name, username, location, etc.)
            $user->update($request->only([
                'name',  // 'name' is the username
                'username',  // The actual username field
                'location',
                'website',
                'phone',
                'bio',
            ]));

            // Handle the profile image upload (only one image)
            if ($request->hasFile('avatar')) {
                $filePath = Helper::uploadImage($request->file('avatar'), 'users', $user->avatar ?? null);
                $user->avatar = $filePath;
                $user->save();
            }

            // Handle the cover image upload (only one image)
            if ($request->hasFile('cover_image')) {
                $coverImagePath = Helper::uploadImage($request->file('cover_image'), 'users/covers', $user->cover_image);
                $user->cover_image = $coverImagePath;
                $user->save();
            }

            // Commit the transaction if everything is successful
            DB::commit();

            // Return success response with updated user data
            return $this->success([
                'user' => [
                    'username' => $user->username,
                    'location' => $user->location,
                    'website' => $user->website,
                    'avatar' => $user->avatar, // The user's profile image
                    'cover_image' => $user->cover_image, // The user's cover image
                    'phone' => $user->phone,
                    'bio' => $user->bio,
                ],
            ], 'User updated successfully', 200);
        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
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
