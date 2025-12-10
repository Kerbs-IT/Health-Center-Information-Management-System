<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,...$roles): Response
    {
        // 1. Check if user is logged in
        if (!Auth::check()) {
            abort(403, 'You do not have access to this page'); // or redirect to login
        }

        // 2. Check if role matches
        $userRole = Auth::user()->role;

        if (!in_array($userRole, $roles)) {
            abort(403, 'You do not have access to this page'); // or 403
        }
        return $next($request);
    }
}
