<?php

namespace ShabuShabu\Harness;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;
use ShabuShabu\Harness\Validators\{ValidateLatitude, ValidateLongitude};

class HarnessServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/harness.php' => config_path('harness.php'),
            ], 'config');
        }

        $this->validator()->extend('longitude', ValidateLongitude::class . '@validate');
        $this->validator()->extend('latitude', ValidateLatitude::class . '@validate');
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/harness.php', 'harness');
    }

    /**
     * @return \Illuminate\Validation\Factory
     */
    protected function validator(): Factory
    {
        return $this->app['validator'];
    }
}
