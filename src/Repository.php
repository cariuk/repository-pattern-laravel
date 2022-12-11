<?php

namespace RepositoryPatternLaravel;

use RepositoryPatternLaravel\Traits\Creation;
use RepositoryPatternLaravel\Traits\Deletation;
use RepositoryPatternLaravel\Traits\Reading;
use RepositoryPatternLaravel\Traits\Relationable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class Repository implements
    \RepositoryPatternLaravel\Contracts\Repository,
    \RepositoryPatternLaravel\Contracts\Reading,
    \RepositoryPatternLaravel\Contracts\Creation,
    \RepositoryPatternLaravel\Contracts\Deletation
{
    use Creation;
    use Deletation;
    use Reading;
    use Relationable;

    /**
     * model
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * builder
     *
     * @var \Illuminate\Database\Eloquent\Builder $builder
     */
    protected $builder;

    /**
     * get model for execution
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * set query builder
     */
    public function setBuilder(Builder $builder): void
    {
        $this->builder = $builder;
    }

    /**
     * get query builder
     */
    public function getBuilder(): Builder
    {
        return $this->builder ?? $this->getModel()::query();
    }
}
