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
    public function index(Request $request)
    {
        $user_id = auth()->user()->id;
        if ($request->user_id) {
            $user_id = $request->user_id;
        }
        $post = Post::where('user_id', $user_id)
            ->with(['comments', 'likes', 'tags'])
            ->orderBy('created_at', 'DESC')
            ->paginate(7);

        return $this->success($post, 'Comment fetch successfully!', 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|image', // Ensure the uploaded file is an image
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

        // Upload the image
        $image_url = null;
        if ($request->hasFile('image')) {
            $image_url = Helper::uploadImage($request->image, 'post');
        }

        // Create the post
        $post = Post::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'file_url' => $image_url,
        ]);

        // Extract hashtags from description
        preg_match_all('/#(\w+)/', $request->description, $matches);
        $hashtags = $matches[1];

        foreach ($hashtags as $tagText) {
            Tag::create([
                'post_id' => $post->id,
                'text' => $tagText
            ]);
        }

        return $this->success($post, 'Post created successfully!', 201);
    }

    public function forYou(Request $request)
    {
        $userId = auth()->id();

        // Get suggested users to follow
        $followingIds = Follow::where('user_id', $userId)
            ->pluck('follower_id')
            ->toArray();

        $usersToFollow = User::whereNotIn('id', $followingIds)
            ->inRandomOrder()
            ->where('is_admin', false)
            ->take(5)
            ->get();
        $usersToFollow->transform(function ($user) use ($followingIds) {
            $user->is_follow = in_array($user->id, $followingIds);
            return $user;
        });

        // Get paginated posts
        $posts = Post::whereNotIn('user_id', $followingIds)
            ->where('user_id', '!=', $userId)
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
