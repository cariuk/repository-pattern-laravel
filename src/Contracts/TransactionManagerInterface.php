<?php

namespace RepositoryPatternLaravel\Contracts;

/**
 * Transaction Manager Interface
 *
 * Provides abstraction for database transaction handling,
 * following Dependency Inversion Principle
 */
interface TransactionManagerInterface
{
    /**
     * Begin a new database transaction
     */
    public function begin(): void;

    /**
     * Commit the active database transaction
     */
    public function commit(): void;

    /**
     * Rollback the active database transaction
     */
    public function rollback(): void;

    /**
     * Execute a callback within a transaction
     *
     * @param callable $callback
     * @return mixed
     * @throws \Throwable
     */
    public function transaction(callable $callback): mixed;
}