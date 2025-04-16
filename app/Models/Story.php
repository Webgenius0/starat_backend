<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    protected $fillable = ['user_id', 'content', 'file_url'];

    public function getFileUrlAttribute($value)
    {
        return $value ? url($value) : null;
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
