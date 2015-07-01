<?php

namespace EspadaV8\ClosureTable;

use EspadaV8\ClosureTable\Console\ClosureTableCommand;
use Illuminate\Support\ServiceProvider;

/**
 * ClosureTable service provider.
 *
 * @package EspadaV8\ClosureTable
 */
class ClosureTableServiceProvider extends ServiceProvider
{
    /**
     * Current library version.
     */
    const VERSION = 4;

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Here we register commands for artisan
        $this->app['command.closuretable'] = $this->app->share(function ($app) {
            return new ClosureTableCommand;
        });

        $this->app['command.closuretable.make'] = $this->app->share(function ($app) {
            return $app['EspadaV8\ClosureTable\Console\MakeCommand'];
        });

        $this->commands('command.closuretable', 'command.closuretable.make');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
