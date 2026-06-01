<?php

namespace RepositoryPatternLaravel\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface Reading
{
    /**
     * Get a list of model instances
     *
     * @param Request $request
     * @param bool|null $shouldPaginate
     * @return Collection|LengthAwarePaginator
     */
    public function getList(Request $request, ?bool $shouldPaginate = null): Collection | LengthAwarePaginator;

    /**
     * Get a single model instance by ID
     *
     * @param Request $request
     * @param int|string $id
     * @param \Closure|null $modifier Optional query modifier
     * @param bool $skipDefaultFilter Skip default filter if true
     * @return Model
     * @throws \Throwable
     */
    public function getDetail(Request $request, int|string $id, ?\Closure $modifier = null, bool $skipDefaultFilter = false): Model;
}
