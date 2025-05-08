<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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
        $search = $request->input('search');
        $users = User::where('name', 'LIKE', "%{$search}%")->get();
        $tags = Tag::where('text', 'LIKE', "%{$search}%")->with(['post'])->get();

        $data = [
            'users' => $users,
            'tags' => $tags,
        ];
        return $this->success($data, 'Data fetched successfully!', 200);
    }

    public function tranding()
    {
        $most_related = DB::table('tags')
            ->select('text', DB::raw('COUNT(*) as usage_count'))
            ->whereNotNull('text')
            ->groupBy('text')
            ->orderByDesc('usage_count')
            ->limit(5)
            ->get();

        return $this->success($most_related, 'Data fetched successfully!', 200);
    }
}
