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
