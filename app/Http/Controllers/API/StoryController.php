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

        // Step 1: Get all the users the auth user follows
        $followedUserIds = Follow::where('user_id', $authUser->id)->pluck('follower_id');
        $blockedUserIds = StoryBlocked::where('user_id', $authUser->id)->pluck('blocked_user_id');
        $mutedUserIds = StoryMute::where('user_id', $authUser->id)->pluck('mute_user_id');
        $reportedUserIds = StoryReport::where('user_id', $authUser->id)->pluck('report_user_id');

        // Step 2: Create final valid user list (include self)
        $validUserIds = $followedUserIds
            ->diff($blockedUserIds)
            ->diff($mutedUserIds)
            ->diff($reportedUserIds)
            ->push($authUser->id) // always include self
            ->unique();

        // Step 3: Get users with at least one story
        $usersWithStories = User::whereIn('id', $validUserIds)
            ->whereHas('story') // ensure they have at least one story
            ->with(['story' => function ($query) {
                $query->latest()->take(1); // only get latest story
            }])
            ->select('id', 'name')
            ->get()
            ->map(function ($user) use ($authUser) {
                $user->story = $user->story->map(function ($story) use ($authUser, $user) {
                    $story->is_me = $user->id === $authUser->id;
                    return $story;
                });
                return $user;
            });

        // Step 4: Separate out your story to put it at the top (only if it exists)
        $myStory = $usersWithStories->firstWhere('id', $authUser->id);
        $usersWithStories = $usersWithStories->reject(fn($user) => $user->id === $authUser->id);

        $result = collect();
        if ($myStory) {
            $result->push($myStory); // add your story first
        }

        $result = $result->merge($usersWithStories); // followed users' stories follow

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
            $media = Helper::s3upload('story', $request->media);
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
            'react' => 'nullable|in:angry,wow,sad,care,funny,love,like',
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

    public function all($id)
    {
        $authUser = auth()->user();

        // Step 1: Get all the users the auth user follows
        $followedUserIds = Follow::where('user_id', $authUser->id)->pluck('follower_id');
        $blockedUserIds = StoryBlocked::where('user_id', $authUser->id)->pluck('blocked_user_id');
        $mutedUserIds = StoryMute::where('user_id', $authUser->id)->pluck('mute_user_id');
        $reportedUserIds = StoryReport::where('user_id', $authUser->id)->pluck('report_user_id');

        // Step 3: Create final valid user list
        $validUserIds = $followedUserIds
            ->diff($blockedUserIds)
            ->diff($mutedUserIds)
            ->diff($reportedUserIds)
            ->push($authUser->id) // always include self
            ->unique();

        // Step 4: Only take users who actually have stories
        $storyUserIds = Story::whereIn('user_id', $validUserIds)
            ->groupBy('user_id')
            ->orderByRaw('MAX(created_at) DESC') // recent first
            ->pluck('user_id')
            ->values();

        // Step 5: Security check - if user is not in valid story list
        if (!$storyUserIds->contains($id)) {
            return $this->error([], 'You are not allowed to view this story.', 403);
        }

        // Step 6: Get previous & next user ID
        $currentIndex = $storyUserIds->search($id);
        $nextUserId = ($currentIndex + 1 < $storyUserIds->count()) ? $storyUserIds[$currentIndex + 1] : null;
        $prevUserId = ($currentIndex - 1 >= 0) ? $storyUserIds[$currentIndex - 1] : null;

        // Step 7: Get all stories of the selected user
        $otherStories = Story::where('user_id', $id)
            ->orderByDesc('id')
            ->with(['user'])
            ->get();

        return $this->success([
            'stories' => $otherStories,
            'previous_user_id' => $prevUserId ?? 0,
            'next_user_id' => $nextUserId ?? 0,
        ], 'Successfully!', 200);
    }


    public function reactShow($id)
    {
        // Check if the authenticated user owns this story
        $story = Story::find($id);

        if (!$story) {
            return $this->error([], 'Story not found', 404);
        }

        if ($story->user_id !== auth()->id()) {
            return $this->error([], 'Unauthorized access', 403);
        }
        // Fetch all reactions for the story with user info
        $reactions = StoryReact::where('story_id', $id)
            ->with('user')
            ->orderByDesc('id')
            ->get();

        // Group by user_id
        $groupedReacts = $reactions->groupBy('user_id')->map(function ($userReactions, $userId) {
            $user = $userReactions->first()->user;

            // Remove user info from each reaction
            $userReactions->each(function ($reaction) {
                unset($reaction->user);
            });

            return [
                'user' => [
                    'avatar' => $user->avatar,
                    'name' => $user->name
                ],
                'reactions' => $userReactions->values()
            ];
        });

        return $this->success($groupedReacts->values(), 'Data Fetch success', 200);
    }


    public function mute(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mute_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        // Check if the user is trying to mute themselves
        if (auth()->user()->id == $request->mute_id) {
            return $this->error([], 'You cannot mute yourself.');
        }
        // Check if the mute already exists
        $existingMute = StoryMute::where('user_id', auth()->user()->id)
            ->where('mute_user_id', $request->mute_id)
            ->first();

        if ($existingMute) {
            $existingMute->delete();
            return $this->success([], 'User removed to mute.', 200);
        }
        $data = StoryMute::create([
            'user_id' => auth()->user()->id,
            'mute_user_id' => $request->mute_id,
        ]);

        return $this->success($data, 'User muted successfully.');
    }


    public function block(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'block_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        // Check if the user is trying to mute themselves
        if (auth()->user()->id == $request->block_id) {
            return $this->error([], 'You cannot blocked yourself.');
        }
        // Check if the mute already exists
        $existingMute = StoryBlocked::where('user_id', auth()->user()->id)
            ->where('blocked_user_id', $request->block_id)
            ->first();

        if ($existingMute) {
            $existingMute->delete();
            return $this->success([], 'User removed to blocked.', 200);
        }
        $data = StoryBlocked::create([
            'user_id' => auth()->user()->id,
            'blocked_user_id' => $request->block_id,
        ]);

        return $this->success($data, 'User Blocked successfully.');
    }

    public function report(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'report_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        // Check if the user is trying to mute themselves
        if (auth()->user()->id == $request->report_id) {
            return $this->error([], 'You cannot report yourself.');
        }

        // Check if the mute already exists
        $existingMute = StoryReport::where('user_id', auth()->user()->id)
            ->where('report_user_id', $request->report_id)
            ->first();

        if ($existingMute) {
            $existingMute->delete();
            return $this->success([], 'Remove user to report', 200);
        }

        // Create the mute record
        $data = StoryMute::create([
            'user_id' => auth()->user()->id,
            'report_user_id ' => $request->report_id,
        ]);

        return $this->success($data, 'User report successfully.');
    }
}
