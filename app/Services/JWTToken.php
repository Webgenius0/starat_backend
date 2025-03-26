<?php

namespace App\Services;

use Tymon\JWTAuth\Facades\JWTAuth;

class JWTToken
{
    public static function createTokenForPasswordReset($user)
    {
        // Create the token with all the custom claims defined in the User model's getJWTCustomClaims method
        $token = JWTAuth::fromUser($user);  // No need to manually pass 'type' claim here

        return $token;
    }
}
