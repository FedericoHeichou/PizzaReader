<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnforceHtml {
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        $response = $next($request);
        // Some proxy cache ignores application/json
        if($response instanceof JsonResponse && $request->header('X-Requested-With') === 'Axios'){
            $response->header('Content-Type', 'text/html');
        }
        return $response;
    }
}
