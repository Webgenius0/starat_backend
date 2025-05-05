<?php

namespace App\Http\Controllers\API;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Reel;
use App\Models\User;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $authUser = auth()->user();
        if ($request->user_id) {
            $authUser = User::find($request->user_id);
        }
        // Fetch reels with user only (no need to load their followings)
        $data = $this->reels->where('user_id', $authUser->id)->with('user')
            ->withCount(['likes', 'comments'])
            ->orderBy('created_at', 'DESC')
            ->latest()
            ->get();
        $data->transform(function ($reel) use ($authUser) {
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

    public function timeline()
    {
        $authUser = auth()->user();

        // Fetch reels with user only (no need to load their followings)
        $data = $this->reels->with('user')
            ->withCount(['likes', 'comments'])
            ->orderBy('created_at', 'DESC')
            ->paginate(5);

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
            $reel = Helper::s3upload('reels', $request->file('reel'));
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

    public function shareCount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url|max:255',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $url = $request->input('url');
        $path = parse_url($url, PHP_URL_PATH);
        $segments = explode('/', trim($path, '/'));
        $slug = end($segments);

        $reel = $this->reels->where('slug', $slug)->first();

        if (!$reel) {
            return $this->error([], 'Reel not found.', 404);
        }
        $reel->share = $reel->share + 1;
        $reel->save();

        return $this->success($reel, 'Share count updated');
    }

    public function personal($id)
    {
        $data = $this->reels->where('user_id', auth()->user()->id)->get()->pluck('id');
        // Step 6: Get previous & next user ID
        $currentIndex = $data->search($id);
        $nextUserId = ($currentIndex + 1 < $data->count()) ? $data[$currentIndex + 1] : null;
        $prevUserId = ($currentIndex - 1 >= 0) ? $data[$currentIndex - 1] : null;

        $reel = Reel::find($id);
        return $this->success([
            'stories' =>  $reel,
            'previous_reel_id' => $prevUserId ?? 0,
            'next_reel_id' => $nextUserId ?? 0,
        ], 'Successfully!', 200);
    }


    public function destroy(Request $request)
    {
        $error = $this->check($request->reel_id);

        if ($error) {
            return $error;
        }

        $reel = $this->reels->find($request->reel_id);

        if (!$reel) {
            return response()->json(['message' => 'Reel not found'], 404);
        }

        // Delete reel video from S3
        if ($reel->file_url) {
            // Extract S3 relative path if video_url is a full URL
            $path = parse_url($reel->file_url, PHP_URL_PATH);
            $path = ltrim($path, '/'); // remove leading slash

            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }
        }
        $reel->delete();

        return $this->success([], 'Reel Deleted Successfully!', 200);
    }


    private function check($id)
    {
        $reels = $this->reels->find($id);

        if (!$reels) {
            return $this->error([], 'Post Not Found!');
        }

        if ($reels->user_id != auth()->user()->id) {
            return $this->error([], 'You are not authorized to delete this post.');
        }

        return null;
    }
}
