<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use App\Models\Reel;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LikeController extends Controller
{
    use apiresponse;

    public function index(Request $request)
    {
        $user_id = auth()->user()->id;
        if ($request->user_id) {
            $user_id = $request->user_id;
        }
        $likes = Like::where('user_id', $user_id)
            ->with('likeable')
            ->orderBy('created_at', 'DESC')
            ->get();
        return $this->success($likes, 'Bookmark added successfully!', 200);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'likeable_id' => 'required|integer',
            'likeable_type' => 'required|in:post,reel',
            'type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $model = $request->likeable_type === 'post' ? Post::class : Reel::class;
        $like = Like::where([
            'user_id' => auth()->user()->id,
            'likeable_id' => $request->likeable_id,
            'likeable_type' => $model
        ])->first();

        if ($like) {
            // If a like exists, delete it (unlike)
            $like->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Like removed successfully!',
            ], 200);
        }

        // If no like exists, create a new one
        $bookmark = Like::create([
            'user_id' => auth()->user()->id,
            'likeable_id' => $request->likeable_id,
            'likeable_type' => $model,
            'type' => $request->type,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Like added successfully!',
            'data' => $bookmark
        ], 200);
    }
}
