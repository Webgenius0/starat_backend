<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoryReport extends Model
{
    protected $fillable = [
        'user_id',
        'report_user_id',
        'created_at'
    ];
}
