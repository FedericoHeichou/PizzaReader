<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AuthenticateAdmin {
    public function handle($request, Closure $next) {
        if(!Auth::check()){
            return redirect('login');
        } elseif (!Auth::user()->hasPermission('admin')) {
            return redirect('/');
        }
        return $next($request);
    }
}
