<?php

namespace RepositoryPatternLaravel\Contracts;

/**
 * Exception Factory Interface
 *
 * Provides abstraction for exception creation,
 * following Dependency Inversion Principle
 */
interface ExceptionFactoryInterface
{
    /**
     * Create a not found exception
     *
     * @param string $message
     * @return \Throwable
     */
    public function notFound(string $message): \Throwable;

    /**
     * Create a bad request exception
     *
     * @param string $message
     * @return \Throwable
     */
    public function badRequest(string $message): \Throwable;

    /**
     * Create a runtime exception
     *
     * @param string $message
     * @return \Throwable
     */
    public function runtime(string $message): \Throwable;
}