<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mention extends Model
{
    protected $fillable = ['user_id', 'post_id', 'mentioned_id'];
}
