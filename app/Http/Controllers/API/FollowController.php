<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FollowController extends Controller
{
    use apiresponse;
    public function index()
    {
        $userId = auth()->user()->id;
        $followers = Follow::where('follower_id', $userId)
            ->with(['follower'])
            ->orderBy('created_at', 'DESC')
            ->get()
            ->map(function ($follow) {
                $follow->is_follow = false; // Add is_follow to each item
                return $follow;
            });
        return $this->success($followers, 'Data Fetch Successfully!', 200);
    }

    public function following()
    {
        $userId = auth()->user()->id;

        // Get the list of users the authenticated user is following
        $followers = Follow::where('user_id', $userId)
            ->with(['follower']) // eager load the follower relationship
            ->orderBy('created_at', 'DESC')
            ->get()
            ->map(function ($follow) {
                $follow->is_follow = true; // Add is_follow to each item
                return $follow;
            });

        return $this->success($followers, 'Data Fetch Successfully!', 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'follower_id' => 'required|integer|exists:users,id|different:auth()->id()', // Don't allow the user to follow themselves
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user_id = auth()->id();
        $follower_id = $request->input('follower_id');

        $existingFollow = Follow::where('user_id', $user_id)
            ->where('follower_id', $follower_id)
            ->first();

        if ($existingFollow) {
            $existingFollow->delete();
            $existingFollow->is_follow = false;
            return $this->success($existingFollow, 'Unfollowed the user', 200);
        }

        $follow = Follow::create([
            'user_id' => $user_id,
            'follower_id' => $follower_id
        ]);
        $follow->is_follow = true;

        return $this->success($follow, 'You are now following this user', 200);
    }

    public function whoToFollow(Request $request)
    {
        $userId = auth()->id();

        $followingIds = Follow::where('user_id', $userId)
            ->pluck('follower_id')
            ->toArray();
        // Get IDs of users the authenticated user is following

        // Get suggested users to follow (excluding already followed users and self)
        $usersToFollow = User::whereNotIn('id', $followingIds)
            ->inRandomOrder()
            ->where('is_admin', false)
            ->take(5)
            ->get();
        $usersToFollow->transform(function ($user) use ($followingIds) {
            $user->is_follow = in_array($user->id, $followingIds);
            return $user;
        });

        // Get posts only from followed users and self
        $posts = Post::whereIn('user_id', $followingIds)
            ->where('user_id', '!=', $userId)
            ->with(['user', 'tags','images'])
            ->withCount(['likes', 'comments', 'repost'])
            ->with(['bookmarks' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->latest()
            ->paginate(5);

        // Add bookmark and interaction statuses
        $posts->getCollection()->transform(function ($post) {
            $post->is_bookmarked = $post->bookmarks->isNotEmpty();
            $post->is_repost = $post->repost->isNotEmpty();
            $post->is_likes = $post->likes->isNotEmpty();
            unset($post->repost);
            unset($post->bookmarks);
            unset($post->likes);
            return $post;
        });

        // Suggested users block
        $suggestedUsersItem = (object)[
            'type' => 'suggested_users',
            'users' => $usersToFollow,
        ];

        // Append suggested users item to posts
        $posts->setCollection(
            $posts->getCollection()->push($suggestedUsersItem)
        );

        return $this->success([
            'posts' => $posts,
        ], 'Data fetched successfully!', 200);
    }
}
