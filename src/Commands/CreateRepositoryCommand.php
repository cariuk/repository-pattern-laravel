<?php

namespace RepositoryPatternLaravel\Commands;

use InvalidArgumentException;
use Illuminate\Console\GeneratorCommand;

class CreateRepositoryCommand extends GeneratorCommand
{
    protected $signature = 'make:repository {model}';

    protected $description = 'Create a repository file';

    protected $type = 'Repository';

    /**
     * Specify your Stub's location.
     */
    protected function getStub()
    {
        return  __DIR__ . '/Stubs/repository.stub';
    }

    /**
     * The root location where your new file should
     * be written to.
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Repositories';
    }

    protected function getNameInput()
    {
        return str_replace('.', '/', trim($this->argument('model').'Repository'));
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);
        return $this->replaceArguments($stub);
    }

    /**
     * Replace the arguments for the given stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function replaceArguments($stub)
    {
        $model = $this->argument('model');
        $modelClass = $this->parseModel($model);
        $repository = $this->defineBaseRepository($modelClass);

        $replace = [
            '{{ namespacedModel }}' => $modelClass,
            '{{ model }}' => class_basename($modelClass),
            '{{ baseRepository }}' => $repository['base_repository'],
            '{{ namespacedBaseRepository }}' => $repository['namespacedBaseRepository']
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $stub
        );
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        return $this->qualifyModel($model);
    }

    protected function defineBaseRepository($model)
    {
        $soft_deleting = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model));

        if ($soft_deleting) {
            $repository = [
                'base_repository' => 'RepositorySoftDelete',
                'namespacedBaseRepository' => 'RepositoryPatternLaravel\RepositorySoftDelete',
            ];
        } else {
            $repository = [
                'base_repository' => 'Repository',
                'namespacedBaseRepository' => 'RepositoryPatternLaravel\Repository',
            ];
        }

        return $repository;
    }
}