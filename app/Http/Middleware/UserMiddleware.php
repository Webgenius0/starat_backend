<?php

namespace App\Http\Middleware;

use App\Traits\apiresponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
{
    use apiresponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user->is_admin == true) {
            return $this->error([], 'You are not authorized to access this route.', 401);
        }
        if (is_null($user->email_verified_at)) {
            return $this->error([], 'This account not verified', 401);
        }

        return $next($request);
    }
}
