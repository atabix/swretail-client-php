<?php

namespace SWRetail;

use Illuminate\Support\ServiceProvider;
use SWRetail\Commands\SWRetailConfigCommand;
use SWRetail\Commands\SWRetailVersionCommand;

class SWRetailServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/swretail.php' => config_path('swretail.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/swretail.php', 'swretail');

        $this->app->bind('command.swretail:config', SWRetailConfigCommand::class);
        $this->app->bind('command.swretail:version', SWRetailVersionCommand::class);

        $this->commands([
            'command.swretail:config',
            'command.swretail:version',
        ]);
    }
}
