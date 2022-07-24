<?php

declare(strict_types=1);

namespace MigrationManager\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(
            static function (): void {
                Route::middleware('api')
                    ->prefix('api')
                    ->group(base_path('routes/api.php'));
                
                Route::middleware('api')
                    ->prefix('api/user')
                    ->namespace('MigrationManager\\Http\\Controllers\\Api\\User\\')
                    ->group(base_path('routes/api/user.php'));
                
                Route::middleware('web')
                    ->group(base_path('routes/web.php'));
            },
        );
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for(
            'api', static function (Request $request) {
                return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
            },
        );
    }
}
