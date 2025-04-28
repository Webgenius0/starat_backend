<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'description', 'user_id', 'file_url'];


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
        return $this->hasMany(StoryImage::class,'post_id');
    }
}
