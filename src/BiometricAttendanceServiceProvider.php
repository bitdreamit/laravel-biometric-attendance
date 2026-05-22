<?php
namespace Bitdreamit\BiometricAttendance;

use Illuminate\Support\ServiceProvider;
use Bitdreamit\BiometricAttendance\Services\AttendanceProcessorService;

class BiometricAttendanceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/biometric.php', 'biometric');
        $this->app->singleton(AttendanceProcessorService::class);
    }

    public function boot(): void
    {
        // Publish config
        $this->publishes([__DIR__.'/../config/biometric.php' => config_path('biometric.php')], 'biometric-config');
        // Publish migrations
        $this->publishes([__DIR__.'/../database/migrations' => database_path('migrations')], 'biometric-migrations');
        // Publish views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'biometric');
        $this->publishes([__DIR__.'/../resources/views' => resource_path('views/vendor/biometric')], 'biometric-views');

        $this->loadRoutes();
    }

    private function loadRoutes(): void
    {
        // API routes
        \Illuminate\Support\Facades\Route::prefix(config('biometric.api_prefix'))
            ->middleware(config('biometric.api_middleware'))
            ->group(__DIR__.'/../routes/api.php');

        // Web routes
        if (config('biometric.web_enabled', true)) {
            \Illuminate\Support\Facades\Route::prefix(config('biometric.web_prefix'))
                ->middleware(config('biometric.web_middleware'))
                ->name('biometric.')
                ->group(__DIR__.'/../routes/web.php');
        }
    }
}
