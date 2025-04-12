<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Post;
use App\Traits\apiresponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    use apiresponse;
    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
        ]);

        $user = auth()->user();
        $post = Post::findOrFail($validated['post_id']);

        // Prevent user from bookmarking their own post (optional)
        if ($post->user_id == $user->id) {
            return $this->error([], 'You cannot bookmark your own post.', 403);
        }

        // Check if already bookmarked
        $alreadyBookmarked = Bookmark::where('user_id', $user->id)
            ->where('bookmarkable_id', $post->id)
            ->where('bookmarkable_type', Post::class)
            ->exists();

        if ($alreadyBookmarked) {
            return $this->error([], 'Post already bookmarked.', 409);
        }

        // Bookmark it
        $user->bookmarks()->create([
            'bookmarkable_id' => $post->id,
            'bookmarkable_type' => Post::class,
        ]);

        return $this->success([], 'Post bookmarked successfully.', 200);
    }
}
