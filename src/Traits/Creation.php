<?php

namespace RepositoryPatternLaravel\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait Creation
{
    /**
     * fillable data model.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * create.
     */
    public function create(FormRequest|array $request): Model
    {
        $object = new $this->model();
        $data = is_array($request) ? $request : $request->only($this->getFillable('create'));
        $object->fill($this->getDataSave($data, 'create'));

        DB::beginTransaction();

        try {
            $object->save();

            if (method_exists($this, 'onCreated')) {
                call_user_func_array([$this, 'onCreated'], [$request, $object]);
            }

            if (method_exists($this, 'onSaved')) {
                call_user_func_array([$this, 'onSaved'], [$request, $object]);
            }

            DB::commit();

            return $object;
        } catch (\Exception $error) {
            DB::rollBack();
            throw new BadRequestHttpException($error->getMessage());
        }
    }

    /**
     * get fiellable fields
     */
    protected function getFillable($method = null)
    {
        return $this->fillable;
    }

    /**
     * get data for save action.
     *
     * @param string $action
     */
    public function getDataSave(array $data, $action): array
    {
        return $data;
    }

    /**
     * update.
     *
     * @param mixed $id
     */
    public function update(FormRequest $request, $id, \Closure $modifier = null, $skipDefaultFilter = false): Model
    {
        if (!method_exists($this, 'getDetail')) {
            throw new \RuntimeException('This method required getDetail in Reading trait');
        }

        $object = $this->getDetail($request, $id, $modifier, $skipDefaultFilter);
        $data = $request->only($this->getFillable('update'));
        $object->fill($this->getDataSave($data, 'update'));

        DB::beginTransaction();

        try {
            $object->save();

            if (method_exists($this, 'onUpdated')) {
                call_user_func_array([$this, 'onUpdated'], [$request, $object]);
            }

            if (method_exists($this, 'onSaved')) {
                call_user_func_array([$this, 'onSaved'], [$request, $object]);
            }

            DB::commit();

            return $object;
        } catch (\Exception $error) {
            DB::rollBack();
            throw new BadRequestHttpException($error->getMessage());
        }
    }
}