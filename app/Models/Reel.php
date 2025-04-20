<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reel extends Model
{
    protected $fillable = ['title', 'description', 'user_id', 'file_url', 'duration'];
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function getFileUrlAttribute($value)
    {
        return $value ? url($value) : null;
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
