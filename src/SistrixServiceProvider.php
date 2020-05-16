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
        $this->publishes([
            __DIR__.'/config/sistrix.php' => config_path('sistrix.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/sistrix.php', 'sistrix');

        $sistrixConfig = config('sistrix');


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

        $this->app->alias(Sistrix::class, 'sistrix');
    }
}
