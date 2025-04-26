<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;
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
    public function index()
    {
        $tags = $this->tag->select('text')->distinct()->get();
        return $this->success($tags, 'Data Fetch Successfully!', 200);
    }
}
