<?php

namespace RepositoryPatternLaravel\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

interface Repository
{
    /**
     * get model
     */
    public function getModel();

    /**
     * set query builder
     */
    public function setBuilder(Builder $builder): void;

    /**
     * get query builder
     */
    public function getBuilder(): Builder;
}
