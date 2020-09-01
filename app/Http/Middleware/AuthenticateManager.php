<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AuthenticateManager {
    public function handle($request, Closure $next) {
        if (Auth::user() != null && Auth::user()->hasPermission('manager')) {
            return $next($request);
        }
        return redirect('login');
    }
}
