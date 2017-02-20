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
        $this->app->singleton(Footman::class, function ($app) {
            return (new Footman)->setDefaultRequestOption([
                'allow_redirect' => $app['config']->get('footman.allow_redirect'),
                'timeout'        => $app['config']->get('footman.timeout'),
                'request_type'   => $app['config']->get('footman.request_type'),
            ]);
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
}
