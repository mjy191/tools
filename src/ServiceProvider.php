<?php
namespace Mjy191\Tools;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    protected function boot()
    {
        $source = realpath(__DIR__ . '/../config/tools.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([$source => \config_path('tools.php')], 'mjy191-tools');
        }
    }
}
