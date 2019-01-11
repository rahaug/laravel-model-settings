<?php

namespace RolfHaug\ModelSettings;

use Illuminate\Support\ServiceProvider;
use RolfHaug\ModelSettings\Commands\GenerateConfigurationModel;

class ModelSettingsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateConfigurationModel::class
            ]);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }
}
