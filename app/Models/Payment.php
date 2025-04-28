<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable =['user_id','amount','post_id','payment_method','status'];
}
