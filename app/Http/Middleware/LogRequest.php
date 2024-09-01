<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogRequest {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        $user = Auth::check() ? '"[' . Auth::id() . '] ' . Auth::user()->name . '"' : 'guest';

        $response = $next($request);

        if($request->method() === "GET") return $response;

        $log = $request->ip() . ' - ' . $user . ' - "' . $request->method() . ' ' . $request->getRequestUri() . '" - "' .
            $request->server('HTTP_USER_AGENT') . '"';


        $context = [];
        $forbidden = ["_token", "_method", "password", "timezone", "new_password", "password_confirmation", "current_password"];
        foreach ($request->post() as $key => $val) {
            if(!in_array($key, $forbidden)) $context[$key] = $val;
        }

        Log::channel('admin')->info($log, $context);

        return $response;
    }
}
