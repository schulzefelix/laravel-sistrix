<?php

namespace SchulzeFelix\Sistrix;

use SchulzeFelix\Sistrix\Exceptions\InvalidConfiguration;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;
use Illuminate\Foundation\Application as LaravelApplication;

class SistrixServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app instanceof LaravelApplication) {
            $this->publishes([
                __DIR__.'/config/laravel-sistrix.php' => config_path('laravel-sistrix.php'),
            ], 'config');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('laravel-sistrix');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/laravel-sistrix.php', 'laravel-sistrix');

        $sistrixConfig = config('laravel-sistrix');


        $this->app->bind(SistrixClient::class, function () use ($sistrixConfig) {
            if (empty($sistrixConfig['key'])) {
                throw InvalidConfiguration::keyNotSpecified();
            }
            return SistrixClientFactory::createForConfig($sistrixConfig);

        });

        $this->app->bind(Sistrix::class, function () use ($sistrixConfig) {
            $client = app(SistrixClient::class);
            return new Sistrix($client);
        });

        $this->app->alias(Sistrix::class, 'laravel-sistrix');
    }
}
