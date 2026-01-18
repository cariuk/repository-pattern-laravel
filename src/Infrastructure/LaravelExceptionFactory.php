<?php

namespace RepositoryPatternLaravel\Infrastructure;

use RepositoryPatternLaravel\Contracts\ExceptionFactoryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Laravel Exception Factory Implementation
 *
 * Concrete implementation using Symfony HTTP exceptions
 */
class LaravelExceptionFactory implements ExceptionFactoryInterface
{
    public function notFound(string $message): \Throwable
    {
        return new NotFoundHttpException($message);
    }

    public function badRequest(string $message): \Throwable
    {
        return new BadRequestHttpException($message);
    }

    public function runtime(string $message): \Throwable
    {
        return new \RuntimeException($message);
    }
}