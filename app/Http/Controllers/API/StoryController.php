<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\Story;
use App\Models\StoryReact;
use App\Models\User;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoryController extends Controller
{
    use apiresponse;

    public function followerStory()
    {
        $authUser = auth()->user();

        // Get IDs of users the authenticated user follows
        $followedUserIds = Follow::where('follower_id', $authUser->id)->pluck('user_id');

        // Fetch followed users who have a story
        $followedUsersWithStories = User::whereIn('id', $followedUserIds)
            ->whereHas('story')
            ->with(['story' => function ($query) {
                $query->latest()->take(1);
            }])
            ->select('id', 'name')
            ->get()
            ->map(function ($user) use ($authUser) {
                // Attach is_me field to each story
                $user->story = $user->story->map(function ($story) use ($authUser, $user) {
                    $story->is_me = $user->id === $authUser->id;
                    return $story;
                });
                return $user;
            });

        // Fetch the authenticated user's latest story
        $myStory = $authUser->story()->latest()->first();

        if ($myStory) {
            $myStory->is_me = true; // Add is_me = true
            $myData = [
                'id' => $authUser->id,
                'name' => $authUser->name,
                'story' => [$myStory],
            ];
        } else {
            $myData = null;
        }

        // Prepend own story (if exists) to the beginning of the collection
        $result = collect($followedUsersWithStories);
        if ($myData) {
            $result->prepend((object) $myData);
        }

        return $this->success($result->values(), 'Data Fetched Successfully!', 200);
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
            'media' => 'required|mimes:jpg,jpeg,png,mp4,avi,mkv|max:10240',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();

        if ($request->media) {
            $media = Helper::uploadImage($request->media, 'story');
        }

        $story = Story::create([
            'user_id' => $user->id,
            'content' => $request->content,
            'file_url' => $media,
        ]);

        return $this->success($story, 'Storie Created Successfully!', 200);
    }

    public function react(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'user_id' => 'required|exists:users,id',
            'story_id' => 'required|string',
            'react' => 'nullable|in:love,haha',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $story_react = StoryReact::create([
            'user_id' => auth()->user()->id,
            'story_id' => $request->story_id,
            'type' => $request->react ?? 'love',
        ]);

        return $this->success($story_react, 'Successfully!', 200);
    }

    public function specific($id)
    {
        $story = Story::where('user_id', $id)
            ->with('user:id,name,avatar')
            ->withCount('react')
            ->orderBy('created_at', 'DESC')
            ->get();
        return $this->success($story, 'Successfully!', 200);
    }
}
