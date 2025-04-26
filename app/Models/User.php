<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Namu\WireChat\Traits\Chatable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Collection;

class User extends Authenticatable implements JWTSubject
{

    use Notifiable;
    use HasRoles;
    use Chatable;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'avatar',
        'name',
        'phone',
        'birthday',
        'otp',
        'email',
        'username',
        'password',
        'status',
        'is_admin',
        'cover_image',
        'bio',
        'location',
        'website'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getAvatarAttribute($value)
    {
        return $value ? url($value) : null;
    }
    public function getCoverImageAttribute($value)
    {
        return $value ? url($value) : null;
    }


    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Implement JWTSubject methods
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Typically the user ID
    }

    public function getJWTCustomClaims()
    {
        return []; // Add any custom claims here
    }

    public function canCreateChats(): bool
    {
        return $this->hasVerifiedEmail();
    }


    public function canCreateGroups(): bool
    {
        return $this->hasVerifiedEmail() === true;
    }

    public static function searchChatables(string $query): ?Collection
    {
        $searchableFields = ['name']; // Fields that can be searched
        return User::where(function ($queryBuilder) use ($searchableFields, $query) {
            foreach ($searchableFields as $field) {
                $queryBuilder->orWhere($field, 'LIKE', '%' . $query . '%');
            }
        })
            ->limit(20) // Limit to 20 results
            ->get(); // Get results
    }

    //backend
    public function reportuser()
    {
        return $this->hasMany(ReportUser::class, 'reported_user_id');
    }

    //block users
    public function blockuser()
    {
        return $this->hasMany(BlockUser::class, 'blocked_user_id');
    }


    public function followers()
    {
        return $this->hasMany(Follow::class, 'user_id');
    }

    // The users that the authenticated user follows
    public function following()
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    public function story()
    {
        return $this->hasMany(Story::class, 'user_id');
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'reposts', 'user_id', 'post_id')->withTimestamps();
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}
