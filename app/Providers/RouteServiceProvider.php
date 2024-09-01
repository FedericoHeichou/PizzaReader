<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $base_uri = parse_url(config('app.url'), PHP_URL_PATH);
        $base_uri = trim(preg_replace('/\/+/', '/', $base_uri), '/');
        if (!empty($base_uri)) {
            $base_uri .= '/';
        }

        RateLimiter::for('api', function (Request $request) {
            $api_rate_limit_per_minute = config('app.api_rate_limit_per_minute');
            if ($api_rate_limit_per_minute > 0) {
                return Limit::perMinute($api_rate_limit_per_minute)->by($request->user()?->id ?: $request->ip());
            } else {
                return Limit::none();
            }
        });

        $this->routes(function () use ($base_uri) {
            Route::middleware(isset($_COOKIE[config('session.cookie')]) ? 'web': 'api')
                ->prefix($base_uri . 'api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->prefix($base_uri)
                ->group(base_path('routes/web.php'));

            Route::middleware(isset($_COOKIE[config('session.cookie')]) ? 'web' : 'api')
                ->prefix($base_uri)
                ->namespace($this->namespace)
                ->group(base_path('routes/fe.php'));
        });
    }
}
