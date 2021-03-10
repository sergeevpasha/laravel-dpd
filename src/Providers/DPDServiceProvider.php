<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use SergeevPasha\DPD\Libraries\DPDClient;

class DPDServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/dpd.php', 'dpd');
        $this->app->singleton(DPDClient::class, fn() => new DPDClient(config('dpd.user'), config('dpd.key')));
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'dpd');
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    __DIR__ . '/../config/dpd.php' => config_path('dpd.php'),
                ],
                'config'
            );
        }
    }

    /**
     * Register routes
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        Route::group(
            $this->routeConfiguration(),
            function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            }
        );
    }

    /**
     * Routes Configuration
     *
     * @return array<mixed>
     */
    protected function routeConfiguration(): array
    {
        return [
            'prefix'     => config('dpd.prefix'),
            'middleware' => config('dpd.middleware'),
        ];
    }
}
