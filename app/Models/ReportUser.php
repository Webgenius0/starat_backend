<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportUser extends Model
{
    protected $fillable = [
        'user_id',
        'reported_user_id',
        'report',
    ];

    /**
     * Get reported User
     */
    public function reported_user()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function block()
    {
        return $this->belongsTo(BlockUser::class, 'reported_user_id', 'blocked_user_id');
    }
}
