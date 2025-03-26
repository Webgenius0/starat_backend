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

        $bookmark = Like::firstOrCreate([
            'user_id' => auth()->user()->id,
            'likeable_id' => $request->likeable_id,
            'likeable_type' => $model,  // Removed the extra space here
            'type' => $request->type,
        ]);

        return $this->success($bookmark, 'Bookmark added successfully!', 201);
    }

}
