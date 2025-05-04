<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\Tag;
use App\Models\User;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use function Laravel\Prompts\select;

class TagsController extends Controller
{
    use apiresponse;

    private $tag;
    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        $tags = $this->tag
            ->select('text')
            ->when($keyword, function ($query, $keyword) {
                $query->where('text', 'like', '%' . $keyword . '%');
            })
            ->distinct()
            ->get();

        return $this->success($tags, 'Tags fetched successfully!', 200);
    }

    public function suggestedFollwer(Request $request)
    {
        $keyword = $request->input('keyword');

        $suggestedUsers = User::where('id', '!=', auth()->id())
            ->whereNotIn('id', function ($query) {
                $query->select('follower_id')
                    ->from('follows')
                    ->where('user_id', auth()->id());
            })
            ->when($keyword, function ($query, $keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            })
            ->select('id', 'username','name','avatar')
            ->get();

        return $this->success($suggestedUsers, 'Suggestions fetched successfully!', 200);
    }
}
