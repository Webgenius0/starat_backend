<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoryMute extends Model
{
    protected $fillable = [
        'user_id',
        'mute_user_id',
        'created_at'
    ];
}
