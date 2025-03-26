<?php

// app/Models/SocialMedia.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model
{
    protected $guarded = [];  

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

