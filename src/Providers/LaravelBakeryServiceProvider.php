<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Providers;

use Illuminate\Support\ServiceProvider;
use Simlux\LaravelBakery\Console\Commands\ModelCommand;
use Simlux\LaravelBakery\Console\Commands\ViewCommand;

/**
 * Class LaravelBakeryServiceProvider
 *
 * @package Simlux\LaravelBakery\Providers
 */
class LaravelBakeryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/laravel-bakery.php' => config_path('laravel-bakery.php'),
        ]);


        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-bakery');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ModelCommand::class,
                ViewCommand::class,
            ]);
        }
    }
}
