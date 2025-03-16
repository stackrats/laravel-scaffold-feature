<?php

namespace Stackrats\LaravelScaffoldFeature;

use Illuminate\Support\ServiceProvider;
use Stackrats\LaravelScaffoldFeature\Console\Commands\ScaffoldFeatureCommand;

class ScaffoldFeatureServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ScaffoldFeatureCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../resources/templates/scaffold-feature' => resource_path('templates/vendor/laravel-scaffold-feature'),
            ], 'laravel-scaffold-feature');
        }
    }
}