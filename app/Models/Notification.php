<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'message', 'title', 'is_read'];


    public function notifiable()
    {
        return $this->morphTo();
    }
}
