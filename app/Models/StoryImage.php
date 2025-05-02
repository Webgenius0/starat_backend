<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StoryImage extends Model
{
    protected $fillable = ['post_id', 'file_url'];

    public function getFileUrlAttribute($value)
    {
        return $value ? Storage::disk('s3')->temporaryUrl($value, now()->addMinutes(30)) : null;
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
