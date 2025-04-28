<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoryImage extends Model
{
    protected $fillable = ['post_id', 'file_url'];

    public function getFileUrlAttribute($value)
    {
        return $value ? url($value) : null;
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
