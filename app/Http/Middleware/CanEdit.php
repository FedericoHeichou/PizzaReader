<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Models\Comic;

class CanEdit {
    public function handle($request, Closure $next) {
        $comic = is_numeric($request->route('comic')) ? Comic::find($request->route('comic')) : Comic::slug($request->route('comic'));
        if(!Auth::check()){
            return redirect('login');
        } elseif (!Auth::user()->canEdit($comic ? $comic->id : null)) {
            abort(403);
        }
        return $next($request);
    }
}
