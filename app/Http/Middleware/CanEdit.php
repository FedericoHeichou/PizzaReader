<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Comic;

class CanEdit {
    public function handle($request, Closure $next) {
        $comic = Comic::slug($request->route('comic'));
        if (Auth::user() != null && Auth::user()->canEdit($comic ? $comic->id : null)) {
            return $next($request);
        }
        return redirect('login');
    }
}
