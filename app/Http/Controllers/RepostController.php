<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Repost;
use App\Models\User;
use App\Traits\apiresponse;
use Illuminate\Http\Request;

class RepostController extends Controller
{
    use apiresponse;

    public function index()
    {
        $user = auth()->user()->id;
        $data = Repost::where('user_id', $user)->select('post_id')->with('posts')->get();
        return $this->success($data, 'Successfully!', 200);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
        ]);

        $user = auth()->user();
        $post = Post::findOrFail($validated['post_id']);

        if ($post->user_id == $user->id) {
            return $this->error([], 'You are not the creator of this post.', 403);
        }


        $alreadyReposted = $user->posts()->where('post_id', $post->id)->exists();

        if ($alreadyReposted) {
            return $this->error([], 'You have already reposted this post.', 409);
        }

        $user->posts()->attach($post->id);

        return $this->success([], 'Post synced (reposted) successfully.', 200);
    }
}
