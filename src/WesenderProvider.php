<?php

namespace Ravelino\Wesender;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Ravelino\Wesender\Exceptions\InvalidConfigException;

class WesenderProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
		
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/wesender.php', 'wesender');

        $this->publishes([
            __DIR__.'/../config/wesender.php' => config_path('wesender.php'),
        ]);

        $this->app->bind(WesenderConfig::class, function () {
            return new WesenderConfig($this->app['config']['wesender']);
        });

        $this->app->singleton(function (Application $app) {
            /** @var WesenderConfig $config */
            $config = $app->make(WesenderConfig::class);


            throw InvalidConfigException::missingConfig();
        });

        $this->app->singleton(Wesender::class, function (Application $app) {
            return new Wesender(
                $app->make(WesenderConfig::class)
            );
        });

        $this->app->singleton(WesenderChannel::class, function (Application $app) {
            return new WesenderChannel(
                $app->make(Wesender::class),
                $app->make(Dispatcher::class)
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            WesenderConfig::class,
            WesenderChannel::class,
        ];
    }
}
