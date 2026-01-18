<?php

namespace RepositoryPatternLaravel\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

trait Deletation
{
    /**
     * Delete a model instance
     *
     * @param Request $request
     * @param int|string $id
     * @param \Closure|null $modifier
     * @param bool $skipDefaultFilter
     * @return Model
     * @throws \Throwable
     */
    public function delete(Request $request, int|string $id, ?\Closure $modifier = null, bool $skipDefaultFilter = false): Model
    {
        $object = $this->getDetail($request, $id, $modifier, $skipDefaultFilter);

        try {
            return $this->getTransactionManager()->transaction(function () use ($object, $request) {
                $object->delete();
                $this->onDeleted($request, $object);
                return $object;
            });
        } catch (QueryException $e) {
            $modelName = class_basename($this->model);
            throw $this->getExceptionFactory()->badRequest(
                "{$modelName} with id {$id} cannot be deleted. It may have related records."
            );
        }
    }

    /**
     * Hook called after a record is deleted
     *
     * Override this method to add custom logic after deletion
     *
     * @param Request $request
     * @param Model $object
     * @return void
     */
    protected function onDeleted(Request $request, Model $object): void
    {
        // Hook for child classes to override
    }

    /**
     * Get detail method (required from Reading trait)
     *
     * @param Request $request
     * @param int|string $id
     * @param \Closure|null $modifier
     * @param bool $skipDefaultFilter
     * @return Model
     */
    abstract protected function getDetail(Request $request, int|string $id, ?\Closure $modifier = null, bool $skipDefaultFilter = false): Model;
}
