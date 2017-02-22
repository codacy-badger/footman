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
        $config = $this->getFootmanConfiguration();

        $this->app->singleton(Footman::class, function ($app) use ($config) {
            return new Footman($config);
        });

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

    private function getFootmanConfiguration()
    {
        return collect($this->app['config']->get('footman'))->filter(function ($value, $key) {
            return !is_null($value);
        })->toArray();
    }
}
