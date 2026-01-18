<?php

namespace RepositoryPatternLaravel\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

trait Creation
{
    /**
     * Override fillable fields (optional)
     *
     * By default, fillable is taken from the model.
     * Only override this if you need different fillable for repository operations.
     *
     * @var array|null
     */
    protected ?array $fillable = null;

    /**
     * Create a new model instance
     *
     * @param FormRequest|array $request
     * @return Model
     * @throws \Throwable
     */
    public function create(FormRequest|array $request): Model
    {
        $modelClass = $this->model;
        $object = new $modelClass();
        $data = is_array($request) ? $request : $request->only($this->getFillable('create'));
        $object->fill($this->getDataSave($data, 'create'));

        return $this->getTransactionManager()->transaction(function () use ($object, $request) {
            $object->save();

            $this->onCreated($request, $object);
            $this->onSaved($request, $object);

            return $object;
        });
    }

    /**
     * Update an existing model instance
     *
     * @param FormRequest|Request $request
     * @param int|string $id
     * @param \Closure|null $modifier
     * @param bool $skipDefaultFilter
     * @return Model
     * @throws \Throwable
     */
    public function update(FormRequest|Request $request, int|string $id, ?\Closure $modifier = null, bool $skipDefaultFilter = false): Model
    {
        $object = $this->getDetail($request, $id, $modifier, $skipDefaultFilter);
        $data = $request instanceof FormRequest ? $request->only($this->getFillable('update')) : $request->all();
        $object->fill($this->getDataSave($data, 'update'));

        return $this->getTransactionManager()->transaction(function () use ($object, $request) {
            $object->save();

            $this->onUpdated($request, $object);
            $this->onSaved($request, $object);

            return $object;
        });
    }

    /**
     * Get fillable fields for specific operation
     *
     * If $fillable is not set in repository, it will use the model's fillable.
     * Override this method for operation-specific fillable fields (create vs update).
     *
     * @param string|null $method
     * @return array
     */
    protected function getFillable(?string $method = null): array
    {
        // If repository defines fillable, use it
        if ($this->fillable !== null) {
            return $this->fillable;
        }

        // Otherwise, get from model
        return $this->getModel()->getFillable();
    }

    /**
     * Process data before saving
     *
     * Override this method to transform data before persisting
     *
     * @param array $data
     * @param string $action
     * @return array
     */
    protected function getDataSave(array $data, string $action): array
    {
        return $data;
    }

    /**
     * Hook called after a record is created
     *
     * Override this method to add custom logic after creation
     *
     * @param FormRequest|array $request
     * @param Model $object
     * @return void
     */
    protected function onCreated(FormRequest|array $request, Model $object): void
    {
        // Hook for child classes to override
    }

    /**
     * Hook called after a record is updated
     *
     * Override this method to add custom logic after update
     *
     * @param FormRequest|Request $request
     * @param Model $object
     * @return void
     */
    protected function onUpdated(FormRequest|Request $request, Model $object): void
    {
        // Hook for child classes to override
    }

    /**
     * Hook called after a record is created or updated
     *
     * Override this method to add custom logic after any save operation
     *
     * @param FormRequest|array|Request $request
     * @param Model $object
     * @return void
     */
    protected function onSaved(FormRequest|array|Request $request, Model $object): void
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
