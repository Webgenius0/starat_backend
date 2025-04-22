<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Post;
use App\Models\Reel;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookmarkController extends Controller
{
    use apiresponse;

    public function index(Request $request)
    {
        $user = auth()->user();
        $userId = $user->id;
        $type = $request->query('type');


        if ($type == 'post') {
            $bookmarkableType = Post::class;
            $ids = Bookmark::where('user_id', $userId)->where('bookmarkable_type', $bookmarkableType)->pluck('bookmarkable_id')->toArray();
            // Get paginated posts
            $posts = Post::whereIn('id', $ids)
                ->with(['user', 'tags'])
                ->withCount(['likes', 'comments', 'repost'])
                ->with(['bookmarks' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }])
                ->latest()
                ->paginate(5);

            // Add bookmark status
            $posts->getCollection()->transform(function ($post) {
                $post->is_bookmarked = $post->bookmarks->isNotEmpty();
                $post->is_repost = $post->repost->isNotEmpty();
                $post->is_likes = $post->likes->isNotEmpty();
                unset($post->repost);
                unset($post->bookmarks);
                unset($post->likes);
                return $post;
            });
            return $this->success($posts, 'Bookmarks fetched successfully.', 200);
        } elseif ($type == 'reel') {
            $bookmarkableType = Reel::class;
            $ids = Bookmark::where('user_id', $userId)->where('bookmarkable_type', $bookmarkableType)->pluck('bookmarkable_id')->toArray();
            $data = Reel::whereIn('id', $ids)->with('user')
                ->withCount(['likes', 'comments'])
                ->orderBy('created_at', 'DESC')
                ->paginate(5);
            $data->getCollection()->transform(function ($reel) use ($user) {
                // Check if the authenticated user follows the reel's user
                $isFollow = $user->following()
                    ->where('follower_id', $reel->user->id)
                    ->exists();

                $reel->user->is_follow = $isFollow;

                // Check if the reel is bookmarked
                $isBookmark = $reel->bookmarks
                    ->where('user_id', $user->id)->isNotEmpty();
                $bookmark_count = $reel->bookmarks->count();
                $reel->bookmarks_count =  $bookmark_count;
                $reel->is_bookmark = $isBookmark;
                $reel->is_likes = $reel->likes->isNotEmpty();


                unset($reel->bookmarks);
                unset($reel->likes);


                return $reel;
            });
            return $this->success($data, 'Data Fetch Successfully!', 200);
        } else {
            return $this->error([], 'Invalid type. Must be "post" or "reel".', 422);
        }
    }




    public function store(Request $request)
    {
        $validated = $request->validate([
            'bookmarkable_id' => 'required|integer',
            'type' => 'required|string|in:post,reel', // only allow these two
        ]);

        $user = auth()->user();

        // Determine the model class based on the 'type'
        $bookmarkableType = match ($validated['type']) {
            'post' => Post::class,
            'reel' => Reel::class,
        };

        // Check if already bookmarked
        $alreadyBookmarked = Bookmark::where('user_id', $user->id)
            ->where('bookmarkable_id', $request->bookmarkable_id)
            ->where('bookmarkable_type', $bookmarkableType)
            ->first();
        if ($alreadyBookmarked) {
            $alreadyBookmarked->delete();
            return $this->success([], 'Bookmarks remove successfully!', 200);
        }

        // Create bookmark
        $user->bookmarks()->create([
            'bookmarkable_id' =>  $request->bookmarkable_id,
            'bookmarkable_type' => $bookmarkableType,
        ]);

        return $this->success([],  'Bookmarked successfully.', 200);
    }
}
