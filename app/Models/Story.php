<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Story extends Model
{
    protected $fillable = ['user_id', 'content', 'file_url', 'slug'];

    public function getFileUrlAttribute($value)
    {
        return $value ? Storage::disk('s3')->temporaryUrl($value, now()->addMinutes(30)) : null;
    }

    public function getSlugAttribute($value)
    {
        return $value ? url('/api/story/story/' . $value) : null;
    }


    public function react()
    {
        return $this->hasMany(StoryReact::class, 'story_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
