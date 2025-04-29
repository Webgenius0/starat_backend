<?php

namespace App\Http\Controllers;

use App\Models\Mention;
use App\Models\Post;
use App\Models\Repost;
use App\Models\User;
use App\Traits\apiresponse;
use Illuminate\Http\Request;

class RepostController extends Controller
{
    use apiresponse;

    public function index(Request $request)
    {
        $user_id = auth()->user()->id;
        if ($request->user_id) {
            $user_id = $request->user_id;
        }
        $reposts = Repost::where('user_id', $user_id)->get()->pluck('post_id');
        // Get paginated posts
        $posts = Post::whereIn('id', $reposts)
            ->with(['user', 'tags','images'])
            ->withCount(['likes', 'comments', 'repost'])
            ->with(['bookmarks' => function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            }])
            ->latest()
            ->get();

        // Add bookmark status
        $posts->transform(function ($post) {
            $post->is_bookmarked = $post->bookmarks->isNotEmpty();
            $post->is_repost = $post->repost->isNotEmpty();
            $post->is_likes = $post->likes->isNotEmpty();
            unset($post->repost);
            unset($post->bookmarks);
            unset($post->likes);
            return $post;
        });
        return $this->success($posts, 'Successfully!', 200);
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
