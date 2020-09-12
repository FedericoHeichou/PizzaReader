<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AuthenticateEditor {
    public function handle($request, Closure $next) {
        if(!Auth::check()){
            return redirect('login');
        } elseif (!Auth::user()->hasPermission('editor')) {
            abort(403);
        }
        return $next($request);
    }
}
