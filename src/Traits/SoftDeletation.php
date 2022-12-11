<?php

namespace RepositoryPatternLaravel\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait SoftDeletation
{
    /**
     * get list of trash object
     */
    public function getTrashList(Request $request, bool $shouldPaginate): Collection | LengthAwarePaginator
    {
        if (!method_exists($this, 'getBuilder')) {
            throw new \RuntimeException('No method getBuilder exists on main class');
        }

        $builder = $this->getBuilder()->onlyTrashed();

        $this->setBuilder($builder);

        if (!method_exists($this, 'getList')) {
            throw new \RuntimeException('This method required getList in Reading trait');
        }

        return $this->getList($request, $shouldPaginate);
    }

    /**
     * force delete.
     *
     * @param mixed $id
     */
    public function forceDelete(Request $request, $id): Model
    {
        if (!method_exists($this, 'getDetail')) {
            throw new \RuntimeException('This method required getDetail in Reading trait');
        }

        $object = $this->getDetail($request, $id, function (Builder &$builder) {
            $builder->onlyTrashed();
        });

        try {
            DB::beginTransaction();

            $object->forceDelete();

            if (method_exists($this, 'onForceDeleted')) {
                call_user_func_array([$this, 'onForceDeleted'], [$request, $object]);
            }

            DB::commit();

            return $object;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new BadRequestHttpException(class_basename($this->model)." with id {$id} can not be deleted");
        }
    }

    /**
     * restore.
     *
     * @param mixed $id
     */
    public function restore(Request $request, $id): Model
    {
        if (!method_exists($this, 'getDetail')) {
            throw new \RuntimeException('This method required getDetail in Reading trait');
        }

        $object = $this->getDetail($request, $id, function (Builder &$builder) {
            $builder->onlyTrashed();
        });

        try {
            DB::beginTransaction();

            $object->restore();

            if (method_exists($this, 'onRestored')) {
                call_user_func_array([$this, 'onRestored'], [$request, $object]);
            }

            DB::commit();

            return $object;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new BadRequestHttpException(class_basename($this->model)." with id {$id} can not be deleted");
        }
    }
}
