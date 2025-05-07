<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    protected $fillable = ['title', 'description', 'user_id', 'file_url'];

    protected $appends = ['is_me'];

    public function getIsMeAttribute()
    {
        // Compare the post's user_id with the authenticated user's ID
        return $this->user_id == Auth::id();
    }

    public function tags()
    {
        return $this->hasMany(Tag::class, 'post_id');
    }
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function repost()
    {
        return $this->hasMany(Repost::class, 'post_id');
    }

    public function images()
    {
        return $this->hasMany(StoryImage::class, 'post_id');
    }
}
