<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\Post;
use App\Models\Reel;
use App\Models\Tag;
use App\Models\User;
use App\Notifications\Notify;
use Illuminate\Support\Facades\Validator;
use App\Traits\apiresponse;
use Illuminate\Http\Request;

class PostController extends Controller
{

    use apiresponse;
    public function index($id)
    {
        $post = Post::with(['comments', 'likes'])->find($id);

        return $this->success($post, 'Comment fetch successfully!', 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|image', // Ensure the uploaded file is an image
            'tag' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        // Get the authenticated user
        $user = auth()->user();

        if ($request->image) {
            $image_url = Helper::uploadImage($request->image, 'post');
        }

        // $user->notify(new Notify('This is a test notification!'));

        // $user = User::find(1);
        // $notifications = $user->notifications; // Get all notifications

        // foreach ($notifications as $notification) {
        //     echo $notification->data['message'];
        // }

        $post = Post::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'file_url' => $image_url,
        ]);
        if ($request->tags) {
            foreach ($request->tags as $tag) {
                $tag = Tag::create([
                    'post_id' => $post->id,
                    'text' => $tag
                ]);
            }
        }
        return $this->success($post, 'Comment added successfully!', 201);
    }

    public function forYou(Request $request)
    {
        $userId = auth()->id();

        // Get suggested users to follow
        $followingIds = Follow::where('follower_id', $userId)
            ->pluck('user_id')
            ->toArray();

        $followingIds[] = $userId; // Exclude the authenticated user

        $usersToFollow = User::whereNotIn('id', $followingIds)
            ->inRandomOrder()
            ->where('is_admin', false)
            ->take(5)
            ->get();

        // Get paginated posts
        $posts = Post::where('user_id', '!=', $userId)
            ->with(['user', 'tags'])
            ->withCount(['likes', 'comments', 'repost'])
            ->with(['bookmarks' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->latest()
            ->paginate(5);

        // Add bookmark status
        $posts->getCollection()->transform(function ($post) {
            $post->is_bookmarked = $post->bookmarks->isNotEmpty();
            $post->is_repost = $post->repost->isNotEmpty();
            $post->is_likes = $post->likes->isNotEmpty();
            unset($post->repost);
            unset($post->bookmarks);
            unset($post->likes);
            return $post;
        });

        // Create a virtual post item to hold suggested users
        $suggestedUsersItem = (object)[
            'type' => 'suggested_users',
            'users' => $usersToFollow,
        ];

        // Append to end of posts collection
        $posts->setCollection(
            $posts->getCollection()->push($suggestedUsersItem)
        );

        return $this->success([
            'posts' => $posts,
        ], 'Data fetched successfully!', 200);
    }
}
