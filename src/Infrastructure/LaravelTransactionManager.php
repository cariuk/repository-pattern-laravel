<?php

namespace RepositoryPatternLaravel\Infrastructure;

use Illuminate\Support\Facades\DB;
use RepositoryPatternLaravel\Contracts\TransactionManagerInterface;

/**
 * Laravel Transaction Manager Implementation
 *
 * Concrete implementation using Laravel's DB facade
 */
class LaravelTransactionManager implements TransactionManagerInterface
{
    public function begin(): void
    {
        DB::beginTransaction();
    }

    public function commit(): void
    {
        DB::commit();
    }

    public function rollback(): void
    {
        DB::rollBack();
    }

    public function transaction(callable $callback): mixed
    {
        return DB::transaction($callback);
    }
}