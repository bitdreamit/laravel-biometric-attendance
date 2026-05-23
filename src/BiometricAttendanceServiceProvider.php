<?php
namespace Bitdreamit\BiometricAttendance;

use Illuminate\Support\ServiceProvider;
use Bitdreamit\BiometricAttendance\Services\AttendanceProcessorService;
use Bitdreamit\BiometricAttendance\Console\Commands\InstallCommand;
use Bitdreamit\BiometricAttendance\Console\Commands\SyncAgentCommand;
use Bitdreamit\BiometricAttendance\Console\Commands\ProcessAttendanceCommand;
use Bitdreamit\BiometricAttendance\Console\Commands\SyncEmployeesCommand;
use Bitdreamit\BiometricAttendance\Console\Commands\AgentStatusCommand;

class BiometricAttendanceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/biometric.php', 'biometric');
        $this->app->singleton(AttendanceProcessorService::class);
    }

    public function boot(): void
    {
        $this->publishes([__DIR__.'/../config/biometric.php'      => config_path('biometric.php')],       'biometric-config');
        $this->publishes([__DIR__.'/../database/migrations'        => database_path('migrations')],         'biometric-migrations');
        $this->publishes([__DIR__.'/../resources/views'            => resource_path('views/vendor/biometric')], 'biometric-views');
        $this->publishes([__DIR__.'/../resources/lang'             => lang_path('vendor/biometric')],       'biometric-lang');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'biometric');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'biometric');

        $this->loadRoutes();

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                SyncAgentCommand::class,
                ProcessAttendanceCommand::class,
                SyncEmployeesCommand::class,
                AgentStatusCommand::class,
            ]);
        }
    }

    private function loadRoutes(): void
    {
        // API routes (Python agent)
        \Illuminate\Support\Facades\Route::prefix(config('biometric.api_prefix'))
            ->middleware(config('biometric.api_middleware'))
            ->group(__DIR__.'/../routes/api.php');

        // Web routes (dashboard)
        if (config('biometric.web_enabled', true)) {
            \Illuminate\Support\Facades\Route::prefix(config('biometric.web_prefix'))
                ->middleware(config('biometric.web_middleware'))
                ->name('biometric.')
                ->group(__DIR__.'/../routes/web.php');
        }
    }
}
