<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Reel;
use App\Models\Tag;
use App\Notifications\Notify;
use Illuminate\Support\Facades\Validator;
use App\Traits\apiresponse;
use Illuminate\Http\Request;

class PostController extends Controller
{

    use apiresponse;
    public function index($id)
    {
        $post = Post::with(['comments', 'likes'])->find($id);

        return $this->success($post, 'Comment fetch successfully!', 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|image', // Ensure the uploaded file is an image
            'tag' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        // Get the authenticated user
        $user = auth()->user();

        if ($request->image) {
            $image_url = Helper::uploadImage($request->image, 'post');
        }

        // $user->notify(new Notify('This is a test notification!'));

        // $user = User::find(1);
        // $notifications = $user->notifications; // Get all notifications

        // foreach ($notifications as $notification) {
        //     echo $notification->data['message'];
        // }

        $post = Post::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'file_url' => $image_url,
        ]);
        if ($request->tags) {
            foreach ($request->tags as $tag) {
                $tag = Tag::create([
                    'post_id' => $post->id,
                    'text' => $tag
                ]);
            }
        }
        return $this->success($post, 'Comment added successfully!', 201);
    }

    public function forYou()
    {
        $post = Post::where('user_id', '!=', auth()->id())
            ->with(['user', 'tags'])
            ->withCount('likes')
            ->withCount('comments')
            ->latest()
            ->get();

        return $this->success($post, 'Data Fetch Succesfully!', 200);
    }
}
