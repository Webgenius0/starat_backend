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
        $story = Follow::where('follower_id', auth()->user()->id)->get()->pluck('user_id');

        $data = User::whereIn('id', $story)
            ->with(['story' => function ($query) {
                $query->latest()->take(1);
            }])
            ->select('id', 'name')->get();

        return $this->success($data, 'Data Fetch Succesfully!', 200);
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
            'post_id' => 'required|string',
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
            'content' => $request->content,
            'react' => $request->react ?? 'love',
        ]);
    }
}
