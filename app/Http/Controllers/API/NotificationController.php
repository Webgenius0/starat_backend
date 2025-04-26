<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use App\Models\Notification;
use App\Models\User;
use App\Services\FirebaseService;
use App\Traits\apiresponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use apiresponse;

    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    // Store FCM token from user device
    public function storeFcmToken(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'token' => 'required|string|unique:fcm_tokens,token',
            'notifications_enabled' => 'boolean'
        ]);

        $data = FcmToken::updateOrCreate(
            ['token' => $request->token],
            [
                'user_id' => $request->user_id,
                'notifications_enabled' => $request->notifications_enabled ?? true
            ]
        );

        return $this->success($data, 'FCM token saved successfully', 200);
    }

    // Send notification to FCM tokens
    public function sendNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'user_id' => 'nullable|exists:users,id'
        ]);

        $tokens = FcmToken::where('notifications_enabled', true)->pluck('token')->toArray();

        if (!empty($tokens)) {
            $this->firebaseService->sendNotification($request->title, $request->body, $tokens);
        }

        if ($request->user_id) {
            $user = User::find($request->user_id);
            $user->notifications()->create([
                'title' => $request->title,
                'message' => $request->body,
            ]);
        }

        return $this->success([], 'Notification sent successfully', 200);
    }

    // Get notifications for a user
    public function getUserNotifications($user_id)
    {
        $notifications = Notification::where('user_id', $user_id)->orderBy('created_at', 'desc')->get();
        return $this->success($notifications, 'Data fetched successfully', 200);
    }

    // Enable or disable user notifications
    public function toggleNotifications(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'enabled' => 'required|boolean',
        ]);

        FcmToken::where('user_id', $request->user_id)->update([
            'notifications_enabled' => $request->enabled
        ]);

        return response()->json(['message' => 'Notification settings updated']);
    }
}
