<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostBoost extends Model
{
    protected $fillable = ['user_id','post_id','budget','status','payment_id','boost_duration_days','boost_start_date'];
}
