<?php

namespace Stackrats\LaravelScaffoldFeature;

use Illuminate\Support\ServiceProvider;
use Stackrats\LaravelScaffoldFeature\Console\Commands\ScaffoldFeatureCommand;

class ScaffoldFeatureServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/laravel-scaffold-feature.php',
            'laravel-scaffold-feature'
        );
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ScaffoldFeatureCommand::class,
            ]);
    
            $this->publishes([
                __DIR__.'/resources/templates/scaffold-feature' => resource_path('templates/vendor/laravel-scaffold-feature'),
            ], 'laravel-scaffold-feature:templates');

            $this->publishes([
                __DIR__.'/config/laravel-scaffold-feature.php' => config_path('laravel-scaffold-feature.php'),
            ], 'laravel-scaffold-feature:config');
        }
    }
}