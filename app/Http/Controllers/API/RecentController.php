<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Recent;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecentController extends Controller
{
    use apiresponse;
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:search,profile',
            'term' => 'nullable|string|max:255',
            'profile_id' => 'nullable|exists:users,id',
        ]);

        // Conditional validation logic
        if ($validated['type'] === 'search' && empty($validated['term'])) {
            return $this->error([], 'Term is required for search type', 422);
        }

        if ($validated['type'] === 'profile' && empty($validated['profile_id'])) {
            return $this->error([], 'Term is required for search type', 422);
        }

        $recent = Recent::create([
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'term' => $validated['type'] === 'search' ? $validated['term'] : null,
            'profile_id' => $validated['type'] === 'profile' ? $validated['profile_id'] : null,
        ]);

        $userRecentIds = Recent::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->skip(20)
            ->take(1000)
            ->pluck('id');

        if ($userRecentIds->isNotEmpty()) {
            Recent::whereIn('id', $userRecentIds)->delete();
        }

        return $this->success($recent, 'Recent saved', 201);
    }

    public function index()
    {
        $recents = Recent::with('profile')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return $this->success($recents, 'Recent Get Successfully!', 200);
    }

    public function destroy($id)
    {
        $recent = Recent::where('user_id', Auth::id())->findOrFail($id);
        $recent->delete();
        return $this->success($recent, 'Recent deleted', 200);
    }

    public function clearAll()
    {
        Recent::where('user_id', Auth::id())->delete();
        return $this->success([], 'All recents cleared', 200);
    }
}
