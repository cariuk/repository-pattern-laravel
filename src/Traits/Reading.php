<?php

namespace RepositoryPatternLaravel\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use RepositoryPatternLaravel\ValueObjects\SortDirection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait Reading
{
    /**
     * Enable/disable pagination
     *
     * @var bool
     */
    protected bool $paginationable = true;

    /**
     * Allow pagination to be optional via request
     *
     * @var bool
     */
    protected bool $optionalPagination = false;

    /**
     * Number of items per page
     *
     * @var int
     */
    protected int $paginatePerPage = 10;

    /**
     * Enable/disable sorting
     *
     * @var bool
     */
    protected bool $sortable = true;

    /**
     * Fields allowed for sorting
     *
     * @var array<string>
     */
    protected array $sortAllowedFields = ['id'];

    /**
     * Default sort field
     *
     * @var string|null
     */
    protected ?string $defaultSortField = null;

    /**
     * Default sort direction (descending)
     *
     * @var bool
     */
    protected bool $defaultSortDescending = false;

    /**
     * Get list of records with filtering, sorting, and pagination
     *
     * @param Request $request
     * @param bool|null $shouldPaginate
     * @return Collection|LengthAwarePaginator
     * @throws \RuntimeException
     */
    public function getList(Request $request, ?bool $shouldPaginate = null): Collection | LengthAwarePaginator
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
     * Get detail of a specific record
     *
     * @param Request $request
     * @param int|string $id
     * @param \Closure|null $modifier
     * @param bool $skipDefaultFilter
     * @return Model
     * @throws NotFoundHttpException
     */
    public function getDetail(Request $request, int|string $id, ?\Closure $modifier = null, bool $skipDefaultFilter = false): Model
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
            throw new NotFoundHttpException(class_basename($this->model) . " with id: {$id} is not found");
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
     * Get sort direction
     *
     * @param Request $request
     * @return string
     * @throws BadRequestHttpException
     */
    protected function getSort(Request $request): string
    {
        if ($request->has('descending')) {
            $direction = SortDirection::fromString($request->descending);
            return $direction->value;
        }

        return SortDirection::fromBoolean($this->defaultSortDescending)->value;
    }

    /**
     * get collection.
     */
    protected function getCollection(Request $request, Builder $builder, bool $shouldPaginate = null): Collection | LengthAwarePaginator
    {
        $usePagination = is_bool($shouldPaginate) ? $shouldPaginate : $this->shouldPaginate($request);

        if ($usePagination) {
            $pagination = $builder->paginate(($request->limit ?: $request->perPage) ?: $this->paginatePerPage);
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
