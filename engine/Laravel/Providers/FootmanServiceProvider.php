<?php

namespace Alshf\Laravel\Providers;

use Alshf\Footman;
use Illuminate\Support\ServiceProvider;

class FootmanServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Create Configuration File
        $this->publishes([
            dirname(__DIR__) . '/Config/footman.php' => config_path('footman.php')
        ], 'footman');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFootman();

        $this->mergeConfigFrom(dirname(__DIR__) . '/Config/footman.php', 'footman');
    }

    /**
     * Register Footman Instance
     *
     * @return void
     */
    protected function registerFootman()
    {
        // Get All Footman Configuration
        $config = $this->getFootmanConfiguration();

        // Create New instance for Footman Class
        $this->app->singleton(Footman::class, function ($app) use ($config) {
            return new Footman($config);
        });

        // Set Alias for Facades
        $this->app->alias(Footman::class, 'footman');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['footman', Footman::class];
    }

    /**
     * Get All footman Configuration
     *
     * @return array
     */
    private function getFootmanConfiguration()
    {
        return collect($this->app['config']->get('footman'))->filter(function ($value, $key) {
            return !is_null($value);
        })->toArray();
    }
}
