<?php

namespace RepositoryPatternLaravel;

use RepositoryPatternLaravel\Contracts\ExceptionFactoryInterface;
use RepositoryPatternLaravel\Contracts\TransactionManagerInterface;
use RepositoryPatternLaravel\Infrastructure\LaravelExceptionFactory;
use RepositoryPatternLaravel\Infrastructure\LaravelTransactionManager;
use RepositoryPatternLaravel\Traits\Creation;
use RepositoryPatternLaravel\Traits\Deletation;
use RepositoryPatternLaravel\Traits\Reading;
use RepositoryPatternLaravel\Traits\Relationable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class Repository implements
    \RepositoryPatternLaravel\Contracts\Repository,
    \RepositoryPatternLaravel\Contracts\Reading,
    \RepositoryPatternLaravel\Contracts\Creation,
    \RepositoryPatternLaravel\Contracts\Deletation
{
    use Creation;
    use Deletation;
    use Reading;
    use Relationable;

    /**
     * Eloquent model class name
     *
     * @var string
     */
    protected string $model;

    /**
     * Query builder instance
     *
     * @var Builder|null
     */
    protected ?Builder $builder = null;

    /**
     * Transaction manager instance
     *
     * @var TransactionManagerInterface
     */
    protected TransactionManagerInterface $transactionManager;

    /**
     * Exception factory instance
     *
     * @var ExceptionFactoryInterface
     */
    protected ExceptionFactoryInterface $exceptionFactory;

    /**
     * Repository constructor
     *
     * Implements proper dependency injection following DIP
     */
    public function __construct(
        ?TransactionManagerInterface $transactionManager = null,
        ?ExceptionFactoryInterface $exceptionFactory = null
    ) {
        // Use default Laravel implementations if not provided
        $this->transactionManager = $transactionManager ?? new LaravelTransactionManager();
        $this->exceptionFactory = $exceptionFactory ?? new LaravelExceptionFactory();
    }

    /**
     * Get model instance
     *
     * @return Model
     */
    public function getModel(): Model
    {
        $modelClass = $this->model;
        return new $modelClass();
    }

    /**
     * Set query builder
     *
     * @param Builder $builder
     * @return void
     */
    public function setBuilder(Builder $builder): void
    {
        $this->builder = $builder;
    }

    /**
     * Get query builder
     *
     * @return Builder
     */
    public function getBuilder(): Builder
    {
        if ($this->builder instanceof Builder) {
            return $this->builder;
        }

        /** @var class-string<Model> $modelClass */
        $modelClass = $this->model;
        return $modelClass::query();
    }

    /**
     * Get transaction manager
     *
     * @return TransactionManagerInterface
     */
    protected function getTransactionManager(): TransactionManagerInterface
    {
        return $this->transactionManager;
    }

    /**
     * Get exception factory
     *
     * @return ExceptionFactoryInterface
     */
    protected function getExceptionFactory(): ExceptionFactoryInterface
    {
        return $this->exceptionFactory;
    }
}
