<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoryBlocked extends Model
{
    protected $fillable = [
        'user_id',
        'blocked_user_id',
        'created_at'
    ];
}
