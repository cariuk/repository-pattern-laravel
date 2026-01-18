<?php

namespace RepositoryPatternLaravel\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

interface Creation
{
    /**
     * Create a new model instance
     *
     * @param FormRequest|array $request
     * @return Model
     * @throws \Throwable
     */
    public function create(FormRequest|array $request): Model;

    /**
     * Update an existing model instance
     *
     * @param FormRequest|Request $request
     * @param int|string $id
     * @param \Closure|null $modifier Optional query modifier
     * @param bool $skipDefaultFilter Skip default filter if true
     * @return Model
     * @throws \Throwable
     */
    public function update(FormRequest|Request $request, int|string $id, ?\Closure $modifier = null, bool $skipDefaultFilter = false): Model;
}
