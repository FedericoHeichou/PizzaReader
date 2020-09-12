<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AllowYourself {
    public function handle($request, Closure $next) {
        if(!Auth::check()){
            return redirect('login');
        } elseif (!(Auth::user()->hasPermission('admin') || Auth::user()->id == $request->route('user'))) {
            return redirect('/');
        }
        return $next($request);
    }
}
