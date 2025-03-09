<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
        /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if (Auth::guest()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (! Auth::user()->hasPermission($permission)) {
            return response()->json(['message' => 'Forbidden: you don\'t have the required permission'], 403);
        }

        return $next($request);
    }
}
