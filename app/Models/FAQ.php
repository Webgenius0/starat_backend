<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    protected $fillable = [
        'que',
        'ans',
        'status',
    ];

    protected $casts = [
        'que' => 'string',
        'ans' => 'string',
        'status' => 'string',
    ];
}
