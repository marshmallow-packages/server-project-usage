<?php

namespace Marshmallow\Server\ProjectUsage;

use Illuminate\Support\ServiceProvider;
use Marshmallow\Server\ProjectUsage\Console\Commands\ShowProjectUsageCommand;
use Marshmallow\Server\ProjectUsage\Console\Commands\PublishPackageUsageCommand;
use Marshmallow\Server\ProjectUsage\Console\Commands\PublishProjectUsageCommand;

class ProjectUsageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    	$this->mergeConfigFrom(__DIR__ . '/../config/project-usage.php', 'project-usage');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    	$this->publishes([
            __DIR__ . '/../config/project-usage.php' => config_path('project-usage.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ShowProjectUsageCommand::class,
                PublishProjectUsageCommand::class,
                PublishPackageUsageCommand::class,
            ]);
        }
    }
}
