<?php

namespace RepositoryPatternLaravel\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface Deletation
{
    /**
     * Delete a model instance
     *
     * @param Request $request
     * @param int|string $id
     * @param \Closure|null $modifier Optional query modifier
     * @param bool $skipDefaultFilter Skip default filter if true
     * @return Model
     * @throws \Throwable
     */
    public function delete(Request $request, int|string $id, ?\Closure $modifier = null, bool $skipDefaultFilter = false): Model;
}
