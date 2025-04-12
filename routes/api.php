<?php

use App\Http\Controllers\API\BlockUserController;
use App\Http\Controllers\API\category\CategoryController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\FollowController;
use App\Http\Controllers\API\HobbyController;
use App\Http\Controllers\API\LikeController;
use App\Http\Controllers\API\MessagingController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\RemainderController;
use App\Http\Controllers\API\ReportUserController;
use App\Http\Controllers\API\SocialmediaController;
use App\Http\Controllers\API\StoryController;
use App\Http\Controllers\API\UserAuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\VideoUploadController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\RepostController;
use App\Models\BlockUser;
use App\Models\User;
use App\Notifications\Notify;
use Illuminate\Support\Facades\Route;

Route::controller(UserAuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');

    // Resend Otp
    Route::post('resend-otp', 'resendOtp');

    // Forget Password
    Route::post('forget-password', 'forgetPassword');
    Route::post('verify-otp', 'checkOTP');
    Route::post('reset-password', 'resetPassword');

    // Google Login
    Route::post('google/login', 'googleLogin');
});

Route::group(['middleware' => ['jwt.verify', 'user']], function () {
    Route::post('logout', [UserAuthController::class, 'logout']);
    Route::get('me', [UserAuthController::class, 'me']);
    Route::post('refresh', [UserAuthController::class, 'refresh']);

    Route::delete('/delete/user', [UserController::class, 'deleteUser']);

    Route::post('change-password', [UserController::class, 'changePassword']);
    Route::post('user-update', [UserController::class, 'updateUserInfo']);

    // All post route
    Route::controller(PostController::class)->prefix('post')->group(function () {
        Route::post('store', 'store');
        Route::get('foryou', 'forYou');
        Route::get('get/{id}', 'index');
    });

    // All hobby route
    Route::controller(HobbyController::class)->prefix('hobby')->group(function () {
        Route::get('get', 'get');
        Route::post('store', 'store');
    });

    // All comment route
    Route::controller(CommentController::class)->prefix('comment')->group(function () {
        Route::post('store', 'store');
        Route::get('get', 'index');
    });

    // All Repost route
    Route::controller(RepostController::class)->prefix('repost')->group(function () {
        Route::post('store', 'store');
        Route::get('get', 'index');
    });

    // All Wishlist route
    Route::controller(WishlistController::class)->prefix('wishlist')->group(function () {
        Route::post('store', 'store');
        Route::get('get', 'index');
    });

    // All Likelist route
    Route::controller(LikeController::class)->prefix('like')->group(function () {
        Route::post('store', 'store');
        Route::get('get', 'index');
    });

    // All Followers
    Route::controller(FollowController::class)->prefix('follow')->group(function () {
        Route::post('store', 'store');
        Route::get('how', 'whoToFollow');
        Route::get('post', 'post');
        Route::get('get', 'index');
    });

    // All Bookmarks
     Route::controller(BookmarkController::class)->prefix('bookmarks')->group(function () {
        Route::post('store', 'store');
        Route::get('get', 'index');
    });

    // All story route
    Route::controller(StoryController::class)->prefix('story')->group(function () {
        Route::post('store', 'store');
        Route::get('get', 'index');
        // home all followe story
        Route::get('followers', 'followerStory');
        Route::get('react/{id}', 'react');
    });

    // All Chat route
    Route::controller(ChatController::class)->group(function () {
        // Route::post('/chat', 'createChat');
        Route::post('/chat/message', 'sendMessage');
        Route::get('/chat/get/messages', 'getConversations');
        Route::post('/chat/group/create', 'groupCreate');
        Route::get('/chat/get', 'getConversations');
        Route::get('/chat/user/covesation/{user}', 'getUserConversation');
        Route::post('/chat/search', 'searchUsers');
    });



    // Get Notifications
    Route::get('/my-notifications', [UserController::class, 'getMyNotifications']);
    Route::get('send-notification', function () {
        $user = User::where('id', Auth::id())->first();
        $user->notify(new Notify("Test Notification"));

        //Send fire base notification
        // $device_tokens = FirebaseTokens::where(function ($query) {
        //     $query->where('user_id', Auth::id())
        //         ->orWhereNull('user_id');
        // })
        //     ->where('is_active', '1')
        //     ->get();
        // $data = [
        //     'message' => $user->name . ' has sent you a notification',
        // ];
        // foreach ($device_tokens as $device_token) {
        //     Helper::sendNotifyMobile($device_token->token, $data);
        // }

        return $response = ['success' => true, 'message' => 'Notification sent successfully'];
    });
});

Route::post('/save-fcm-token', [NotificationController::class, 'storeFcmToken']);
Route::post('/send-notification', [NotificationController::class, 'sendNotification']);
Route::get('/notifications/{user_id}', [NotificationController::class, 'getUserNotifications']);
