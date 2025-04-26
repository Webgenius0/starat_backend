<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\Mention;
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
        // Get paginated posts
        $posts = Post::where('user_id', $user_id)
            ->with(['user', 'tags'])
            ->withCount(['likes', 'comments', 'repost'])
            ->with(['bookmarks' => function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            }])
            ->latest()
            ->get();

        // Add bookmark status
        $posts->transform(function ($post) {
            $post->is_bookmarked = $post->bookmarks->isNotEmpty();
            $post->is_repost = $post->repost->isNotEmpty();
            $post->is_likes = $post->likes->isNotEmpty();
            unset($post->repost);
            unset($post->bookmarks);
            unset($post->likes);
            return $post;
        });
        return $this->success($posts, 'Comment fetch successfully!', 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string',  // Title is no longer required
            'description' => 'nullable|string',  // Description is optional but can be provided
            'image' => 'nullable|image',  // Image is optional but can be provided
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get the authenticated user
        $user = auth()->user();

        // Upload the image (only if image is provided)
        $image_url = null;
        if ($request->hasFile('image')) {
            $image_url = Helper::uploadImage($request->image, 'post');
        }

        // Create the post (title, description, and image_url are optional)
        $post = Post::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'file_url' => $image_url,
        ]);

        // Extract hashtags from description if it exists
        preg_match_all('/#(\w+)/', $request->description, $matches);
        $hashtags = $matches[1];

        // Store hashtags
        foreach ($hashtags as $tagText) {
            Tag::create([
                'post_id' => $post->id,
                'text' => $tagText
            ]);
        }

        // Extract mentions from description if it exists
        preg_match_all('/@(\w+)/', $request->description, $mentionMatches);
        $mentions = $mentionMatches[1];

        // Store mentions (associating with users)
        foreach ($mentions as $mentionText) {
            // Find user by their username or slug (you might need to adjust this based on your user model)
            $mentionedUser = User::where('username', $mentionText)->first();

            // If a user is found, store the mention
            if ($mentionedUser) {
                Mention::create([
                    'post_id' => $post->id,
                    'user_id' => auth()->user()->id,
                    'mentioned_id' => $mentionedUser->id, // The user who created the post
                ]);
            }
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


    public function highlight(Request $request)
    {
        $user_id = auth()->user()->id;
        if ($request->user_id) {
            $user_id = $request->user_id;
        }
        $mentions = Mention::where('mentioned_id', $user_id)->get()->pluck('post_id');
        // Get paginated posts
        $posts = Post::whereIn('id', $mentions)
            ->with(['user', 'tags'])
            ->withCount(['likes', 'comments', 'repost'])
            ->with(['bookmarks' => function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            }])
            ->latest()
            ->get();

        // Add bookmark status
        $posts->transform(function ($post) {
            $post->is_bookmarked = $post->bookmarks->isNotEmpty();
            $post->is_repost = $post->repost->isNotEmpty();
            $post->is_likes = $post->likes->isNotEmpty();
            unset($post->repost);
            unset($post->bookmarks);
            unset($post->likes);
            return $post;
        });
        return $this->success($posts, 'Successfully!', 200);
    }
}
