<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FilterController extends Controller
{
    use apiresponse;
    public function index(Request $request)
    {
        $userId = auth()->user()->id;
        $search = $request->input('search');
        $users = User::where('name', 'LIKE', "%{$search}%")->get();
        $tags = Tag::where('text', 'LIKE', "%{$search}%")->with('post')->get();


        // Get unique post IDs from the matching tags
        $postIds = $tags->pluck('post.id')->filter()->unique()->values();

        // Get matching posts with all required relationships
        $posts = Post::whereIn('id', $postIds)
            ->with(['user', 'tags', 'images'])
            ->withCount(['likes', 'comments', 'repost'])
            ->with(['bookmarks' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->latest()
            ->get();

        $data = [
            'users' => $users,
            'tags' => $posts,
        ];
        return $this->success($data, 'Data fetched successfully!', 200);
    }

    public function suggest(Request $request)
    {
        $search = $request->input('search');
        $users = User::where('name', 'LIKE', "%{$search}%")->select('id', 'name', 'base', 'avatar', 'username')->get();
        $tags = Tag::where('text', 'LIKE', "%{$search}%")->get();
        $data = [
            'users' => $users,
            'trending' => $tags,
        ];
        return $this->success($data, 'Data fetched successfully!', 200);
    }

    public function trending()
    {
        $most_related = DB::table('tags')
            ->select('text', DB::raw('COUNT(*) as usage_count'))
            ->whereNotNull('text')
            ->groupBy('text')
            ->orderByDesc('usage_count')
            ->limit(5)
            ->get();

        $recent = DB::table('recents')
        ->whereNotNull('term')
            ->select('term', DB::raw('COUNT(*) as usage_count'))
            ->groupBy('term')
            ->orderByDesc('usage_count')
            ->limit(5)
            ->get();
        $data = [
            'tags' => $most_related,
            'search' => $recent
        ];
        return $this->success($data, 'Data fetched successfully!', 200);
    }
}
