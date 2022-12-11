<?php

namespace cariuk;

use Illuminate\Support\ServiceProvider;
use KlinikPintar\Commands\CreateRepositoryCommand;

class RepositoryPatternLaravelServiceProvider extends ServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateRepositoryCommand::class,
            ]);
        }
    }
}