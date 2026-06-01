<?php

namespace RepositoryPatternLaravel\ValueObjects;

use Illuminate\Http\Request;

/**
 * Repository Query DTO
 *
 * Data Transfer Object to decouple repository from HTTP Request
 * Follows Dependency Inversion Principle
 */
class RepositoryQuery
{
    public function __construct(
        public readonly ?int $page = null,
        public readonly ?int $limit = null,
        public readonly ?string $sortField = null,
        public readonly SortDirection $sortDirection = SortDirection::ASC,
        public readonly array $relations = [],
        public readonly array $filters = [],
        public readonly ?bool $shouldPaginate = null,
    ) {}

    /**
     * Create from HTTP Request
     *
     * @param Request $request
     * @return self
     */
    public static function fromRequest(Request $request): self
    {
        // Parse sort direction
        $sortDirection = SortDirection::ASC;
        if ($request->has('descending')) {
            if ($request->descending === 'true' || $request->descending === true) {
                $sortDirection = SortDirection::DESC;
            } elseif ($request->descending === 'false' || $request->descending === false) {
                $sortDirection = SortDirection::ASC;
            }
        }

        // Parse relations
        $relations = [];
        if ($request->filled('with')) {
            $with = $request->with;
            $relations = is_array($with) ? $with : explode(',', $with);
        }

        // Parse pagination flag
        $shouldPaginate = null;
        if ($request->has('paginate')) {
            $shouldPaginate = $request->paginate === 'true' || $request->paginate === true;
        }

        return new self(
            page: $request->page ? (int) $request->page : null,
            limit: $request->limit ?? $request->perPage ? (int) ($request->limit ?? $request->perPage) : null,
            sortField: $request->sort,
            sortDirection: $sortDirection,
            relations: $relations,
            filters: $request->except(['page', 'limit', 'perPage', 'sort', 'descending', 'with', 'paginate']),
            shouldPaginate: $shouldPaginate,
        );
    }

    /**
     * Create empty query
     *
     * @return self
     */
    public static function empty(): self
    {
        return new self();
    }

    /**
     * Check if has pagination parameters
     *
     * @return bool
     */
    public function hasPagination(): bool
    {
        return $this->page !== null || $this->limit !== null;
    }

    /**
     * Check if has sorting parameters
     *
     * @return bool
     */
    public function hasSorting(): bool
    {
        return $this->sortField !== null;
    }

    /**
     * Check if has relations to eager load
     *
     * @return bool
     */
    public function hasRelations(): bool
    {
        return !empty($this->relations);
    }

    /**
     * Check if has filters
     *
     * @return bool
     */
    public function hasFilters(): bool
    {
        return !empty($this->filters);
    }
}