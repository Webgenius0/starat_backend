<?php

use App\Http\Controllers\API\BlockUserController;
use App\Http\Controllers\API\category\CategoryController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\FilterController;
use App\Http\Controllers\API\FollowController;
use App\Http\Controllers\API\HobbyController;
use App\Http\Controllers\API\LikeController;
use App\Http\Controllers\API\MessagingController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\ReelsController;
use App\Http\Controllers\API\RemainderController;
use App\Http\Controllers\API\ReportUserController;
use App\Http\Controllers\API\SocialmediaController;
use App\Http\Controllers\API\StoryController;
use App\Http\Controllers\API\TagsController;
use App\Http\Controllers\API\UserAuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\VideoUploadController;
use App\Http\Controllers\API\WishlistController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\RepostController;
use App\Models\BlockUser;
use App\Models\User;
use App\Notifications\Notify;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Search;

Route::controller(UserAuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('register-verify-otp', 'registerCheckOTP');

    Route::post('logout', 'logout');

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
    Route::get('profile/me', [UserAuthController::class, 'profileMe']);
    Route::post('refresh', [UserAuthController::class, 'refresh']);

    Route::delete('/delete/user', [UserController::class, 'deleteUser']);

    // Route::post('change-password', [UserController::class, 'changePassword']);
    Route::post('profile-update', [UserController::class, 'updateUserInfo']);

    // All post route
    Route::controller(PostController::class)->prefix('post')->group(function () {
        Route::post('store', 'store');
        Route::get('foryou', 'forYou');
        Route::get('get', 'index');
        Route::get('highlight', 'highlight');
    });

    // All hobby route
    Route::controller(HobbyController::class)->prefix('hobby')->group(function () {
        Route::get('get', 'get');
        Route::post('store', 'store');
    });

    // All comment route
    Route::controller(CommentController::class)->prefix('comment')->group(function () {
        Route::post('store', 'store');
        Route::get('get/{type}/{id}', 'index');
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
        // Route::get('how', 'whoToFollow');
        Route::get('post', 'whoToFollow');
        Route::get('get', 'index');
        Route::get('following', 'following');
        // Route::get('remove/{id}', 'remove');
    });

    // All Bookmarks
    Route::controller(BookmarkController::class)->prefix('bookmarks')->group(function () {
        Route::post('store', 'store');
        Route::get('get', 'index');
    });


    // All Post Tags
    Route::controller(TagsController::class)->prefix('tags')->group(function () {
        Route::post('get', 'index');
        Route::post('suggested', 'suggestedFollwer');
    });

    // All Reels 
    Route::controller(ReelsController::class)->prefix('reels')->group(function () {
        Route::post('store', 'store');
        Route::get('get', 'index');
        Route::get('/reels/{slug}', 'showBySlug');
        Route::get('timeline', 'timeline');
        Route::post('count', 'shareCount');
    });

    // All story route
    Route::controller(StoryController::class)->prefix('story')->group(function () {
        Route::post('store', 'store');
        Route::get('get', 'index');
        Route::post('mute', 'mute');
        Route::post('block', 'block');
        Route::post('report', 'report');
        Route::get('followers', 'followerStory');
        Route::post('react', 'react');
        Route::get('all/{id}', 'all');
        Route::get('/story/{slug}', 'showBySlug');
        Route::get('/react/show/{id}', 'reactShow');
    });

    // All Chat route
    Route::controller(ChatController::class)->group(function () {
        // Route::post('/chat', 'createChat');
        Route::post('/chat/message', 'sendMessage');
        Route::get('/chat/get/messages', 'getConversations');
        Route::post('/chat/group/create', 'groupCreate');
        Route::get('/chat/get', 'getConversations');
        Route::get('/chat/user/conversation/{user}', 'getUserConversation');
        Route::post('/chat/search', 'searchUsers');
        Route::post('/chat/create/covesation', 'createCovesation');
        Route::post('/chat/block', 'covesationBlock');
    });

    // All Filter 
    Route::controller(FilterController::class)->prefix('search')->group(function () {
        Route::post('get', 'index');
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
