<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AuthenticateManager {
    public function handle($request, Closure $next) {
        if (!Auth::check()) {
            return redirect('login');
        } elseif (!Auth::user()->hasPermission('manager')) {
            return redirect('/');
        }
        return $next($request);
    }
}
