<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AuthenticateAdmin {
    public function handle($request, Closure $next) {
        if (Auth::user() != null && Auth::user()->hasPermission('admin')) {
            return $next($request);
        }
        return redirect('login');
    }
}
