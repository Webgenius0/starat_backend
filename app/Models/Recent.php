<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recent extends Model
{
    protected $fillable = ['user_id', 'type', 'term', 'profile_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->belongsTo(User::class, 'profile_id');
    }
}
