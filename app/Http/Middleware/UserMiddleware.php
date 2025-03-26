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
        if ($user->is_varified == false) {
            return $this->error([], 'You are not varifiyed', 401);
        }

        return $next($request);
    }
}
