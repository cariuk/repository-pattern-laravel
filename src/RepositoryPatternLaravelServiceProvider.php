<?php

namespace Cariuk;

use Illuminate\Support\ServiceProvider;
use Cariuk\Commands\CreateRepositoryCommand;

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