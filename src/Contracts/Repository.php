<?php

namespace RepositoryPatternLaravel\Contracts;

use App\Models\Outlet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

interface Repository
{

    public function __construct();

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
