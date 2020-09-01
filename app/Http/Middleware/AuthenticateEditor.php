<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AuthenticateEditor {
    public function handle($request, Closure $next) {
        if (Auth::user() != null && Auth::user()->hasPermission('editor')) {
            return $next($request);
        }
        return redirect('login');
    }
}
