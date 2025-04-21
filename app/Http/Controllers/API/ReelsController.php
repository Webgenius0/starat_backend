<?php

namespace App\Http\Controllers\API;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Reel;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ReelsController extends Controller
{
    use apiresponse;
    private $reels;
    public function __construct(Reel $reels)
    {
        $this->reels = $reels;
    }

    public function index(Request $request)
    {
        $user_id = auth()->user()->id;
        if ($request->user_id) {
            $user_id = $request->user_id;
        }
        $data = $this->reels->where('user_id', $user_id)->orderBy('created_at', 'DESC')->paginate(7);
        return $this->success($data, 'Data Fetch Successfully!', 200);
    }

    public function timeline()
    {
        $authUser = auth()->user();

        // Fetch reels with user only (no need to load their followings)
        $data = $this->reels->with('user') 
            ->withCount(['likes', 'comments'])
            ->orderBy('created_at', 'DESC')
            ->paginate(7);

        $data->getCollection()->transform(function ($reel) use ($authUser) {
            // Check if the authenticated user follows the reel's user
            $isFollow = $authUser->following()
                ->where('follower_id', $reel->user->id)
                ->exists();

            $reel->user->is_follow = $isFollow;

            // Check if the reel is bookmarked
            $isBookmark = $reel->bookmarks
                ->where('user_id', $authUser->id)->isNotEmpty();
            $bookmark_count = $reel->bookmarks->count();
            $reel->bookmarks_count =  $bookmark_count;
            $reel->is_bookmark = $isBookmark;
            $reel->is_likes = $reel->likes->isNotEmpty();


            unset($reel->bookmarks);
            unset($reel->likes);


            return $reel;
        });

        return $this->success($data, 'Data Fetch Successfully!', 200);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'reel' => 'required|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska|max:51200', // Max 50MB
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $user = auth()->user();

        $slug = Str::slug($user->name . '-' . time());
        // Store the uploaded video file
        if ($request->hasFile('reel')) {
            $reel = Helper::uploadImage($request->file('reel'), 'reels');
            $post = new $this->reels();
            $post->user_id = $user->id;
            $post->title = $request->input('title');
            $post->file_url = $reel;
            $post->slug = $slug;
            $post->save();
            return $this->success($post, 'Reel uploaded successfully.', 200);
        }
        return $this->error([], 'Reel file is missing.', 400);
    }

    public function showBySlug($slug)
    {
        $story = $this->reels->where('slug', $slug)->with('user')->first();
        return $this->success($story, 'Data Send successfully!', 200);
    }
}
