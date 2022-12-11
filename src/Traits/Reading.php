<?php

namespace RepositoryPatternLaravel\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait Reading
{
    /**
     * paginationable.
     *
     * @var bool
     */
    protected $paginationable = true;

    /**
     * optional pagination.
     *
     * @var bool
     */
    protected $optionalPagination = false;

    /**
     * pagination per page.
     *
     * @var int
     */
    protected $paginatePerPage = 10;

    /**
     * sortable.
     *
     * @var bool
     */
    protected $sortable = true;

    /**
     * field allowed to sort.
     *
     * @var array
     */
    protected $sortAllowedFields = ['id'];

    /**
     * default sort field.
     *
     * @var string
     */
    protected $defaultSortField = null;

    /**
     * default sort descending.
     *
     * @var bool
     */
    protected $defaultSortDescending = false;

    /**
     * get list of object
     */
    public function getList(Request $request, bool $shouldPaginate = null): Collection | LengthAwarePaginator
    {
        if (!method_exists($this, 'getBuilder')) {
            throw new \RuntimeException('No method getBuilder exists on main class');
        }

        $builder = $this->getBuilder();

        $this->applyFilter($request, $builder);
        $this->applySort($request, $builder);

        if (method_exists($this, 'applyRelation')) {
            $this->applyRelation($request, $builder);
        }

        return $this->getCollection($request, $builder, $shouldPaginate);
    }

    /**
     * get list of object
     *
     * @throws App\Exceptions\ResourceNotFound
     */
    public function getDetail(Request $request, $id, \Closure $modifier = null, $skipDefaultFilter = false): Model
    {
        if (!method_exists($this, 'getBuilder')) {
            throw new \RuntimeException('No method getBuilder exists on main class');
        }

        $builder = $this->getBuilder();

        $builder->whereId($id);

        if (!$skipDefaultFilter) {
            $this->applyFilter($request, $builder);
        }

        if (is_callable($modifier)) {
            $modifier($builder);
        }

        if (method_exists($this, 'applyRelation')) {
            $this->applyRelation($request, $builder);
        }

        $object = $builder->first();

        if (!$object) {
            throw new  NotFoundHttpException(class_basename($this->model) . " with id: {$id} is not found");
        }

        return $object;
    }

    /**
     * apply filter.
     */
    protected function applyFilter(Request $request, Builder &$builder): void
    {
    }

    /**
     * apply sort.
     */
    protected function applySort(Request $request, Builder &$builder): void
    {
        if ($this->sortable) {
            $sortField = $this->getSortField($request);
            if (!is_null($sortField)) {
                $builder->orderBy($sortField, $this->getSort($request));
            }
        }
    }

    /**
     * get sort field.
     *
     * @return mixed
     */
    protected function getSortField(Request $request): ?string
    {
        if ($request->has('sort')) {
            if ($this->validSortField($request->sort)) {
                return $request->sort;
            }

            throw new BadRequestHttpException("Field {$request->sort} is not allowed for sorting");
        }

        return $this->defaultSortField;
    }

    /**
     * check is given string is valid field for sorting.
     */
    protected function validSortField(string $field): bool
    {
        return in_array($field, $this->sortAllowedFields);
    }

    /**
     * get sort.
     */
    protected function getSort(Request $request): string
    {
        if ($request->has('descending')) {
            if ('true' === $request->descending) {
                return 'DESC';
            }

            if ('false' === $request->descending) {
                return 'ASC';
            }

            throw new BadRequestHttpException('descending should string of true or false');
        }

        return $this->defaultSortDescending ? 'DESC' : 'ASC';
    }

    /**
     * get collection.
     */
    protected function getCollection(Request $request, Builder $builder, bool $shouldPaginate = null): Collection | LengthAwarePaginator
    {
        $usePagination = is_bool($shouldPaginate) ? $shouldPaginate : $this->shouldPaginate($request);

        if ($usePagination) {
            $pagination = $builder->paginate(($request->limit || $request->perPage) ?: $this->paginatePerPage);
            $pagination->appends($request->all());
            return $pagination;
        }

        return $builder->get();
    }

    /**
     * should use pagination.
     */
    protected function shouldPaginate(Request $request): bool
    {
        if (!$this->paginationable) {
            return false;
        }

        if ($this->optionalPagination) {
            return $request->has('page') || $request->has('limit');
        }

        return true;
    }
}
