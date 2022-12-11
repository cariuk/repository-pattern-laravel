<?php

namespace Cariuk\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait Deletation
{
    /**
     * delete.
     *
     * @param mixed $id
     */
    public function delete(Request $request, $id, \Closure $modifier = null, $skipDefaultFilter = false): Model
    {
        if (!method_exists($this, 'getDetail')) {
            throw new \RuntimeException('This method required getDetail in Reading trait');
        }

        $object = $this->getDetail($request, $id, $modifier, $skipDefaultFilter);

        DB::beginTransaction();

        try {
            $object->delete();

            if (method_exists($this, 'onDeleted')) {
                call_user_func_array([$this, 'onDeleted'], [$request, $object]);
            }

            DB::commit();

            return $object;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new BadRequestHttpException(class_basename($this->model)." with id {$id} can not be deleted");
        }
    }
}
