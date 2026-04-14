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
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request,Closure $next,$roles)
    {
         $roles = explode('|', $roles);

    if (!Auth::check()) {
        return redirect('/auth/login');
    }

    if (!in_array(Auth::user()->role, $roles)) {
        abort(403, 'Akses ditolak');
    }

    return $next($request);
    }
}

