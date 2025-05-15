<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\Mention;
use App\Models\Post;
use App\Models\Reel;
use App\Models\StoryImage;
use App\Models\Tag;
use App\Models\User;
use App\Notifications\Notify;
use Illuminate\Support\Facades\Validator;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{

    use apiresponse;

    private $posts;

    public function __construct(Post $post)
    {
        $this->posts = $post;
    }
    public function index(Request $request)
    {
        $user_id = auth()->user()->id;
        if ($request->user_id) {
            $user_id = $request->user_id;
        }
        // Get paginated posts
        $posts = $this->posts->where('user_id', $user_id)
            ->with(['user', 'tags', 'images'])
            ->withCount(['likes', 'comments', 'repost'])
            ->with(['bookmarks' => function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            }])
            ->latest()
            ->get();

        // Add bookmark status
        $posts->transform(function ($post) {
            $post->is_bookmarked = $post->bookmarks->isNotEmpty();
            $post->is_repost = $post->repost->isNotEmpty();
            $post->is_likes = $post->likes->isNotEmpty();
            unset($post->repost);
            unset($post->bookmarks);
            unset($post->likes);
            return $post;
        });
        return $this->success($posts, 'Comment fetch successfully!', 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|array',
            'image.*' => 'image',  // Each file in the array must be an image
        ]);


        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        // Get the authenticated user
        $user = auth()->user();

        // Upload the image (only if image is provided)
        // $image_url = null;
        // if ($request->hasFile('image')) {
        //     $image_url = Helper::uploadImage($request->image, 'post');
        // }
        try {
            $image_urls = [];
            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $imageFile) {
                    // $uploadedUrl = Helper::uploadToS3($imageFile);
                    // Store the file in the 'location_cover' directory on the 's3' disk
                    $uploadedUrl = Storage::disk('s3')->put('post', $imageFile);

                    // If you want the full URL, use 'url' instead of 'put'
                    // $uploadedUrl = Storage::disk('s3')->url($uploadedUrl);

                    $image_urls[] = $uploadedUrl;
                }
            }


            // Create the post (title, description, and image_url are optional)
            $post = $this->posts->create([
                'user_id' => $user->id,
                'title' => $request->title,
                'description' => $request->description,
            ]);

            foreach ($image_urls as $url) {
                StoryImage::create([
                    'post_id' => $post->id,
                    'file_url' => $url,
                ]);
            }

            // Extract hashtags from description if it exists
            preg_match_all('/#(\w+)/', $request->description, $matches);
            $hashtags = $matches[1];

            // Store hashtags
            foreach ($hashtags as $tagText) {
                Tag::create([
                    'post_id' => $post->id,
                    'text' => $tagText
                ]);
            }

            // Extract mentions from description if it exists
            preg_match_all('/@(\w+)/', $request->description, $mentionMatches);
            $mentions = $mentionMatches[1];

            // Store mentions (associating with users)
            foreach ($mentions as $mentionText) {
                // Find user by their username or slug (you might need to adjust this based on your user model)
                $mentionedUser = User::where('username', $mentionText)->first();

                // If a user is found, store the mention
                if ($mentionedUser) {
                    Mention::create([
                        'post_id' => $post->id,
                        'user_id' => auth()->user()->id,
                        'mentioned_id' => $mentionedUser->id, // The user who created the post
                    ]);
                }
            }

            return $this->success($post, 'Post created successfully!', 201);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 'Error');
        }
    }



    public function forYou(Request $request)
    {
        $userId = auth()->id();

        // Get suggested users to follow
        $followingIds = Follow::where('user_id', $userId)
            ->pluck('follower_id')
            ->toArray();

        $usersToFollow = User::whereNotIn('id', $followingIds)
            ->inRandomOrder()
            ->where('is_admin', false)
            ->take(5)
            ->get();
        $usersToFollow->transform(function ($user) use ($followingIds) {
            $user->is_follow = in_array($user->id, $followingIds);
            return $user;
        });

        // Get paginated posts
        $posts = $this->posts->whereNotIn('user_id', $followingIds)
            ->where('user_id', '!=', $userId)
            ->with(['user', 'tags', 'images'])
            ->withCount(['likes', 'comments', 'repost'])
            ->with(['bookmarks' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->latest()
            ->paginate(6);

        // Add bookmark status
        $posts->getCollection()->transform(function ($post) {
            $post->is_bookmarked = $post->bookmarks->isNotEmpty();
            $post->is_repost = $post->repost->isNotEmpty();
            $post->is_likes = $post->likes->isNotEmpty();
            unset($post->repost);
            unset($post->bookmarks);
            unset($post->likes);
            return $post;
        });

        // Create a virtual post item to hold suggested users
        $suggestedUsersItem = (object)[
            'type' => 'suggested_users',
            'users' => $usersToFollow,
        ];

        // Append to end of posts collection
        $posts->setCollection(
            $posts->getCollection()->push($suggestedUsersItem)
        );

        return $this->success([
            'posts' => $posts,
        ], 'Data fetched successfully!', 200);
    }

    public function mention(Request $request)
    {

        try {
            $user = User::where('username', $request->username)->first();
            if (!$user) {
                return $this->error([], 'User not found!');
            }

            $response = [
                'id' => $user->id,
            ];

            return $this->success([
                'user' => $response,
            ], 'User retrieved successfully', 200);
        } catch (\Throwable $th) {
            return $this->error('User not found', 404);
        }
    }


    public function highlight(Request $request)
    {
        $user_id = auth()->user()->id;
        if ($request->user_id) {
            $user_id = $request->user_id;
        }
        $mentions = Mention::where('mentioned_id', $user_id)->get()->pluck('post_id');
        // Get paginated posts
        $posts = $this->posts->whereIn('id', $mentions)
            ->with(['user', 'tags'])
            ->withCount(['likes', 'comments', 'repost'])
            ->with(['bookmarks' => function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            }])
            ->latest()
            ->get();

        // Add bookmark status
        $posts->transform(function ($post) {
            $post->is_bookmarked = $post->bookmarks->isNotEmpty();
            $post->is_repost = $post->repost->isNotEmpty();
            $post->is_likes = $post->likes->isNotEmpty();
            unset($post->repost);
            unset($post->bookmarks);
            unset($post->likes);
            return $post;
        });
        return $this->success($posts, 'Successfully!', 200);
    }

    public function destroy(Request $request)
    {
        $error = $this->check($request->post_id);

        if ($error) {
            return $error;
        }

        $post = $this->posts->find($request->post_id);

        // Delete all related images from S3
        foreach ($post->images as $image) {
            if ($image->file_url) {
                // Extract S3 relative path if file_url is a full URL
                $path = parse_url($image->file_url, PHP_URL_PATH);
                $path = ltrim($path, '/'); // remove leading slash
                if (Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }
            }
            $image->delete();
        }

        // Delete related tags
        Tag::where('post_id', $post->id)->delete();

        // Delete related mentions
        Mention::where('post_id', $post->id)->delete();

        $post->delete();

        return $this->success([], 'Post Deleted Successfully!', 200);
    }


    private function check($id)
    {
        $post = $this->posts->find($id);

        if (!$post) {
            return $this->error([], 'Post Not Found!');
        }

        if ($post->user_id != auth()->user()->id) {
            return $this->error([], 'You are not authorized to delete this post.');
        }

        return null;
    }
}
