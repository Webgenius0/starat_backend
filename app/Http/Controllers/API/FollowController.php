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
    public function index() {}

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
        $existingFollow = Follow::where('user_id', $follower_id)
            ->where('follower_id', $user_id)
            ->exists();

        if ($existingFollow) {
            return $this->error([], 'You are already following this user', 400);
        }
        Follow::create([
            'user_id' => $follower_id,
            'follower_id' => $user_id
        ]);
        return $this->success([], 'You are now following this user', 200);
    }

    public function whoToFollow()
    {
        $authUserId = auth()->id();

        // Get IDs of users the authenticated user is already following
        $followingIds = Follow::where('follower_id', $authUserId)
            ->pluck('user_id');

        $followingIds[] = $authUserId;

        $usersToFollow = User::whereNotIn('id', $followingIds)
            ->inRandomOrder()
            ->where('is_admin', false)
            ->take(10)
            ->get();
        return $this->success($usersToFollow, 'Suggested users to follow', 200);
    }

    public function post()
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

        $posts = Post::whereIn('user_id', $followingIds)
            ->with(['user', 'tags'])
            ->withCount(['likes', 'comments', 'repost'])
            ->with(['bookmarks' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->latest()
            ->paginate(2);
        // Add bookmark status to posts and remove unnecessary data
        $posts->each(function ($post) {
            $post->is_bookmarked = $post->bookmarks->isNotEmpty();
            unset($post->bookmarks);
        });

        return $this->success([
            'posts' => $posts,
            'suggested_users' => $usersToFollow
        ], 'Data Fetch Successfully!', 200);
    }
}
