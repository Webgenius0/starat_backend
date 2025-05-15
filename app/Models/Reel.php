<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Reel extends Model
{
    protected $fillable = ['title', 'description', 'user_id', 'file_url', 'duration', 'slug', 'share'];
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function getFileUrlAttribute($value)
    {
        return $value ? Storage::disk('s3')->temporaryUrl($value, now()->addMinutes(30)) : null;
    }

    public function getSlugAttribute($value)
    {
        return $value ? url('/api/reels/reels/' . $value) : null;
    }

    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
