<?php

namespace RepositoryPatternLaravel;

use Illuminate\Support\ServiceProvider;
use RepositoryPatternLaravel\Commands\CreateRepositoryCommand;

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