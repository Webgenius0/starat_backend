<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Post;
use App\Models\Reel;
use App\Traits\apiresponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    use apiresponse;

    public function index(Request $request)
    {
        $user = auth()->user();

        // Optional filter by type (post or reel)
        $type = $request->query('type');

        // Start the query from user's bookmarks
        $query = $user->bookmarks();

        // Apply filter if 'type' is provided
        if ($type) {
            if (!in_array($type, ['post', 'reel'])) {
                return $this->error([], 'Invalid type. Must be "post" or "reel".', 422);
            }

            // Convert string type to full class name
            $bookmarkableType = match ($type) {
                'post' => Post::class,
                'reel' => Reel::class,
            };

            $query->where('bookmarkable_type', $bookmarkableType);
        }

        // Load the bookmarked models (Post or Reel)
        $bookmarks = $query->with('bookmarkable')->latest()->get();

        return $this->success($bookmarks, 'Bookmarks fetched successfully.');
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

        // Find the bookmarkable item
        $bookmarkable = $bookmarkableType::findOrFail($validated['bookmarkable_id']);

        // Optional: prevent bookmarking your own content
        if ($bookmarkable->user_id == $user->id) {
            return $this->error([], 'You cannot bookmark your own content.', 403);
        }

        // Check if already bookmarked
        $alreadyBookmarked = Bookmark::where('user_id', $user->id)
            ->where('bookmarkable_id', $bookmarkable->id)
            ->where('bookmarkable_type', $bookmarkableType)
            ->first();

        if ($alreadyBookmarked) {
            $alreadyBookmarked->delete();
            return $this->success([], 'Bookmarks remove successfully!', 200);
        }

        // Create bookmark
        $user->bookmarks()->create([
            'bookmarkable_id' => $bookmarkable->id,
            'bookmarkable_type' => $bookmarkableType,
        ]);

        return $this->success([],  ' bookmarked successfully.', 200);
    }
}
