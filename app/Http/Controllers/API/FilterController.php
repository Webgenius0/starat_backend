<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $users = User::where('name', 'LIKE', "%{$search}%")->get();
        $tags = Tag::where('text', 'LIKE', "%{$search}%")->with(['post'])->get();

        return response()->json([
            'users' => $users,
            'tags' => $tags,
        ]);
    }
}
