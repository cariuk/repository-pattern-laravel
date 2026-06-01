<?php

namespace RepositoryPatternLaravel\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait SoftDeletation
{
    /**
     * Get list of soft-deleted (trashed) records
     *
     * @param Request $request
     * @param bool $shouldPaginate
     * @return Collection|LengthAwarePaginator
     */
    public function getTrashList(Request $request, bool $shouldPaginate): Collection | LengthAwarePaginator
    {
        $builder = $this->getBuilder()->onlyTrashed();
        $this->setBuilder($builder);

        return $this->getList($request, $shouldPaginate);
    }

    /**
     * Permanently delete a soft-deleted record
     *
     * @param Request $request
     * @param int|string $id
     * @return Model
     * @throws \Throwable
     */
    public function forceDelete(Request $request, int|string $id): Model
    {
        $object = $this->getDetail($request, $id, function (Builder $builder) {
            $builder->onlyTrashed();
        });

        try {
            return $this->getTransactionManager()->transaction(function () use ($object, $request) {
                $object->forceDelete();
                $this->onForceDeleted($request, $object);
                return $object;
            });
        } catch (QueryException $e) {
            $modelName = class_basename($this->model);
            throw $this->getExceptionFactory()->badRequest(
                "{$modelName} with id {$id} cannot be permanently deleted. It may have related records."
            );
        }
    }

    /**
     * Restore a soft-deleted record
     *
     * @param Request $request
     * @param int|string $id
     * @return Model
     * @throws \Throwable
     */
    public function restore(Request $request, int|string $id): Model
    {
        $object = $this->getDetail($request, $id, function (Builder $builder) {
            $builder->onlyTrashed();
        });

        try {
            return $this->getTransactionManager()->transaction(function () use ($object, $request) {
                $object->restore();
                $this->onRestored($request, $object);
                return $object;
            });
        } catch (QueryException $e) {
            $modelName = class_basename($this->model);
            throw $this->getExceptionFactory()->badRequest(
                "{$modelName} with id {$id} cannot be restored."
            );
        }
    }

    /**
     * Hook called after a record is permanently deleted
     *
     * Override this method to add custom logic after force deletion
     *
     * @param Request $request
     * @param Model $object
     * @return void
     */
    protected function onForceDeleted(Request $request, Model $object): void
    {
        // Hook for child classes to override
    }

    /**
     * Hook called after a record is restored
     *
     * Override this method to add custom logic after restoration
     *
     * @param Request $request
     * @param Model $object
     * @return void
     */
    protected function onRestored(Request $request, Model $object): void
    {
        // Hook for child classes to override
    }

    /**
     * Get list method (required from Reading trait)
     *
     * @param Request $request
     * @param bool|null $shouldPaginate
     * @return Collection|LengthAwarePaginator
     */
    abstract protected function getList(Request $request, ?bool $shouldPaginate = null): Collection | LengthAwarePaginator;

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
