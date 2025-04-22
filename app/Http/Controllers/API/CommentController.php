<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Reel;
use App\Services\Service;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    use apiresponse;
    public function index($type, $id)
    {
        $query = Comment::query();
        if ($type == 'post') {
            $query->where('commentable_type', Post::class);
        } elseif ($type == 'reel') {
            $query->where('commentable_type', Reel::class);
        } else {
            return $this->error([], 'Type Not Metch!');
        }
        $comments = $query->where('user_id', auth()->user()->id)->with('commentable')->get();

        return $this->success($comments, 'Comments fetched successfully!', 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string',
            'commentable_id' => 'required|integer',
            'commentable_type' => 'required|in:post,reel',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }


        $model = $request->commentable_type === 'post' ? Post::class : Reel::class;

        $comment = Comment::create([
            'user_id' => auth()->user()->id,
            'body' => $request->comment,
            'commentable_id' => $request->commentable_id,
            'commentable_type' => $model,
        ]);
        return $this->success($comment, 'Comment added successfully!', 201);
    }
}
