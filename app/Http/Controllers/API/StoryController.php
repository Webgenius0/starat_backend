<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\Story;
use App\Models\StoryBlocked;
use App\Models\StoryMute;
use App\Models\StoryReact;
use App\Models\StoryReport;
use App\Models\User;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StoryController extends Controller
{
    use apiresponse;

    public function followerStory()
    {
        $authUser = auth()->user();

        $followedUserIds = Follow::where('follower_id', $authUser->id)->pluck('user_id');

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



    public function showBySlug($slug)
    {
        $story = Story::where('slug', $slug)->with('user')->first();
        return $this->success($story, 'Data Send successfully!', 200);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'user_id' => 'required|exists:users,id',
            'content' => 'nullable|string',
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
        $slug = Str::slug($user->name . '-' . time());
        $story = Story::create([
            'user_id' => $user->id,
            'content' => $request->content,
            'file_url' => $media,
            'slug' => $slug,
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

    public function all()
    {
        $authUser = auth()->user();

        $followedUserIds = Follow::where('user_id', $authUser->id)->pluck('user_id');
        // Get the list of blocked users.
        $blockedUserIds = StoryBlocked::where('user_id', $authUser->id)->pluck('blocked_user_id');

        // Get the list of muted users.
        $mutedUserIds = StoryMute::where('user_id', $authUser->id)->pluck('mute_user_id');

        // Get the list of report users.
        $reportedUserIds = StoryReport::where('user_id', $authUser->id)->pluck('report_user_id');

        $followedUsersWithStories = User::whereIn('id', $followedUserIds)
            ->whereNotIn('id', $blockedUserIds)
            ->whereNotIn('id', $mutedUserIds)
            ->whereNotIn('id', $reportedUserIds)
            ->whereHas('story')
            ->with(['story.react.user'])
            ->select('id', 'name', 'avatar', 'base')
            ->get();
        return $this->success($followedUsersWithStories, 'Successfully!', 200);
    }

    public function mute($id)
    {
        // Check if the user is trying to mute themselves
        if (auth()->user()->id == $id) {
            return $this->error([], 'You cannot mute yourself.');
        }
        // Check if the mute already exists
        $existingMute = StoryMute::where('user_id', auth()->user()->id)
            ->where('mute_user_id', $id)
            ->first();

        if ($existingMute) {
            return $this->error([], 'User is already muted.');
        }
        $data = StoryMute::create([
            'user_id' => auth()->user()->id,
            'mute_user_id' => $id,
        ]);

        return $this->success([], 'User muted successfully.');
    }


    public function block($id)
    {
        // Check if the user is trying to mute themselves
        if (auth()->user()->id == $id) {
            return $this->error([], 'You cannot blocked yourself.');
        }
        // Check if the mute already exists
        $existingMute = StoryBlocked::where('user_id', auth()->user()->id)
            ->where('blocked_user_id', $id)
            ->first();

        if ($existingMute) {
            return $this->error([], 'User is already blocked.');
        }
        $data = StoryBlocked::create([
            'user_id' => auth()->user()->id,
            'blocked_user_id' => $id,
        ]);

        return $this->success([], 'User Blocked successfully.');
    }

    public function report($id)
    {
        // Check if the user is trying to mute themselves
        if (auth()->user()->id == $id) {
            return $this->error([], 'You cannot report yourself.');
        }

        // Check if the mute already exists
        $existingMute = StoryReport::where('user_id', auth()->user()->id)
            ->where('report_user_id', $id)
            ->first();

        if ($existingMute) {
            return $this->error([], 'User is already report.');
        }

        // Create the mute record
        $data = StoryMute::create([
            'user_id' => auth()->user()->id,
            'report_user_id ' => $id,
        ]);

        return $this->success([], 'User report successfully.');
    }
}
