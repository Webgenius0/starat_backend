<?php

namespace App\Http\Controllers\API;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Reel;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReelsController extends Controller
{
    use apiresponse;
    private $reels;
    public function __construct(Reel $reels)
    {
        $this->reels = $reels;
    }

    public function index()
    {
        $user = auth()->user()->id;
        $data = $this->reels->where('user_id', $user)->orderBy('created_at', 'DESC')->get();
        return $this->success($data, 'Data Fetch Successfully!', 200);
    }

    public function timeline()
    {
        $authUser = auth()->user();

        // Fetch reels with user and their followers
        $data = $this->reels->with('user.following')->withCount(['likes', 'comments'])->orderBy('created_at', 'DESC')->paginate(2);
        $data->getCollection()->transform(function ($reel) use ($authUser) {
            $isFollow = $authUser->following()->where('follower_id', $reel->user->id)->exists();
            $reel->user->is_follow = $isFollow;

            // Check if the authenticated user has bookmarked this reel
            $isBookmark = $reel->bookmarks->where('user_id', $authUser->id)->isNotEmpty();
            $reel->is_bookmark = $isBookmark;
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
        // Store the uploaded video file
        if ($request->hasFile('reel')) {
            $reel = Helper::uploadImage($request->file('reel'), 'reels');
            $post = new $this->reels();
            $post->user_id = auth()->user()->id;
            $post->title = $request->input('title');
            $post->file_url = $reel;
            $post->save();
            return $this->success($post, 'Reel uploaded successfully.', 200);
        }
        return $this->error([], 'Reel file is missing.', 400);
    }
}
