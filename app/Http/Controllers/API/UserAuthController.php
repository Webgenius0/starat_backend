<?php

namespace App\Http\Controllers\API;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\OtpNotification;
use App\Traits\apiresponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserAuthController extends Controller
{
    use apiresponse;

    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login']]);
    // }
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // password confirmation
            'phone' => 'required|numeric', // Ensure phone is unique and numeric
            'birthday' => 'required|date|before:today', // Ensure birthday is a valid date
            'name' => 'required|string|max:255', // Ensure name is provided
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        DB::beginTransaction();
        try {
            $validated = $request->only([
                'username',
                'email',
                'password',
                'phone',
                'birthday',
                'name'
            ]);
            $validated['password'] = bcrypt($validated['password']);
            $validated['otp'] = $this->generateOtp();
            $user = User::create($validated);
            $user->notify(new OtpNotification($validated['otp']));
            DB::commit();
            return $this->success($user->only('id', 'username', 'email', 'phone', 'name'), 'User created successfully. Please check your email for verification.', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage(), 400);
        }
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->error([], 'Invalid credentials.', 401);
            }
        } catch (JWTException $e) {
            return $this->error([], 'Could not create token.', 500);
        }

        $user = auth()->user();

        if (is_null($user->email_verified_at)) {
            return $this->error([], 'Please verify your email address.', 401);
        }

        if ($user->status === 'inactive') {
            return $this->error([], 'Your account is inactive. Please contact the administrator.', 401);
        }

        return $this->success([
            'token' => $token,
        ], 'User logged in successfully.', 200);
    }


    /**
     * Google Login
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function googleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }
        $credentials = $request->only('email');
        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }
        $token = JWTAuth::fromUser($user);
        return $this->success([
            'token' => $this->respondWithToken($token),
        ], 'User logged in successfully.', 200);
    }


    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        $otp = $this->generateOtp();
        $user->notify(new OtpNotification($otp));
        $user->otp = $otp;
        $user->is_varified = false;
        $user->save();

        return $this->success(['otp', $otp], 'Check Your Email for Password Reset Otp', 200);
    }

    /**
     * Reset Password Controller
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
            'otp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = User::where('email', $request->email)->first();

        // Check if the OTP is valid
        if (!$user->otp || !$request->otp == $user->otp) {
            return response()->json([
                'message' => 'Invalid OTP.',
            ], 400);
        }


        // Proceed to reset the password
        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->is_varified = true;
        $user->save();
        $token = JWTAuth::fromUser($user);
        return $this->success($token, 'Password reset successfully.', 200);
    }

    // Resend Otp
    public function resendOtp(Request $request)
    {
        // Validate the email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        // Generate and save new OTP
        $otp = $this->generateOtp();
        $user->otp = $otp;
        $user->save();

        // Send OTP notification
        $user->notify(new OtpNotification($otp));

        return $this->success([], 'Check your email for the password reset OTP.', 200);
    }


    /**
     * Varify User Otp
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);
        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $user = User::where('email', $request->email)->first();

        // // Check if OTP is null or expired
        // if (!$user->otp || !$user->otp_expiry || Carbon::now()->greaterThan($user->otp_expiry)) {
        //     return $this->error([], 'OTP has expired or is not set.', 400);
        // }

        // Check if OTP matches
        if ($user->otp != $request->otp) {
            return $this->error([], 'Invalid OTP.', 400);
        }

        // $user->is_varified = true;
        // $user->otp = null;
        // $user->save();

        // $token = JWTAuth::fromUser($user);

        return $this->success([], 'OTP verified successfully', 200);
    }


    public function registerCheckOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);
        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $user = User::where('email', $request->email)->first();
        $user->is_varified = now();
        $user->otp = null;
        $user->save();

        return $this->success([], 'OTP validated successfully. Your account is now verified.', 200);
    }

    /**
     * Log out the user (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            // Get token from the request
            $token = JWTAuth::getToken();

            if (!$token) {
                return $this->error([], 'Token not provided', 400);
            }

            JWTAuth::invalidate($token);

            return $this->success([], 'Successfully logged out', 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->error([], 'Token is already invalidated', 400);
        } catch (\Exception $e) {
            return $this->error([], 'Could not log out user', 500);
        }
    }


    /**
     * Get the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileMe(Request $request)
    {
        try {
            $user = auth()->user();

            if ($request->filled('user_id') && $request->user_id != $user->id) {
                $user = User::findOrFail($request->user_id);
            }

            $response = [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar ?? null,
                'cover_image' => $user->cover_image ?? null,
                'username' => $user->username,
                'joined' => 'Joined ' . $user->created_at->format('M Y'),
                'follower' => $user->followers()->count(),
                'following' => $user->following()->count(),
                'post' => $user->posts()->count(),
            ];

            return $this->success([
                'user' => $response,
            ], 'User retrieved successfully', 200);
        } catch (ModelNotFoundException $e) {
            return $this->error('User not found', 404);
        }
    }



    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            // Refresh the token
            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            return $this->success([
                'access_token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ], 'Token refreshed successfully', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 400);
        }
    }

    /**
     * Get Token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    public function guard()
    {
        return Auth::guard();
    }
}
