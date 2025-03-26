<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Post;
use App\Models\Reel;
use App\Models\User;
use App\Traits\apiresponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    use apiresponse;

    public function index()
    {
        $user = auth()->user();
        $bookmarkedReels = $user->bookmarks()
            ->where('bookmarkable_type', Reel::class) // Only reels
            ->with('bookmarkable')
            ->get();
        return $this->success($bookmarkedReels, 'Data fetch successfully!', 200);
    }
    public function store(Request $request)
    {
        $request->validate([
            'bookmarkable_id' => 'required|integer',
            'bookmarkable_type' => 'required|in:post,reel',
        ]);

        $model = $request->bookmarkable_type === 'post' ? Post::class : Reel::class;

        $bookmark = Bookmark::firstOrCreate([
            'user_id' => auth()->user()->id,
            'bookmarkable_id' => $request->bookmarkable_id,
            'bookmarkable_type' => $model,
        ]);

        return $this->success($bookmark, 'Bookmark added successfully!', 201);
    }
}
