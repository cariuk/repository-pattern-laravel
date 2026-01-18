<?php

namespace RepositoryPatternLaravel\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

interface Repository
{
    /**
     * Get the model instance
     *
     * @return Model
     */
    public function getModel(): Model;

    /**
     * Set query builder
     *
     * @param Builder $builder
     * @return void
     */
    public function setBuilder(Builder $builder): void;

    /**
     * Get query builder
     *
     * @return Builder
     */
    public function getBuilder(): Builder;
}
