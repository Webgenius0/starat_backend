<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repost extends Model
{
    protected $fillable = ['user_id', 'post_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function posts()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
