<?php

namespace KlinikPintar;

use KlinikPintar\Traits\Creation;
use KlinikPintar\Traits\Deletation;
use KlinikPintar\Traits\Reading;
use KlinikPintar\Traits\Relationable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class Repository implements
    \KlinikPintar\Contracts\Repository,
    \KlinikPintar\Contracts\Reading,
    \KlinikPintar\Contracts\Creation,
    \KlinikPintar\Contracts\Deletation
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

    public function __construct(Builder $builder = null)
    {
        $this->builder = $builder;
    }

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
