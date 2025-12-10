<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check() && Auth::user()->status === 'active'){

            $role = Auth::user()->role;

            switch ($role) {

                case 'patient':
                    return redirect()->route('dashboard.patient');
                case 'nurse':

                    return redirect()->route('dashboard.nurse');
                    break;
                case 'staff':
                    return redirect()->route('dashboard.staff');
                default:
            }
        }
        return $next($request);
    }
}
