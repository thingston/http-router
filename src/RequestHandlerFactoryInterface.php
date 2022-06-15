<?php

declare(strict_types=1);

namespace Thingston\Http\Router;

use Psr\Http\Server\RequestHandlerInterface;

interface RequestHandlerFactoryInterface
{
    /**
     * @param callable $callable
     * @param array<string, string> $arguments
     * @return RequestHandlerInterface
     */
    public static function create(callable $callable, array $arguments = []): RequestHandlerInterface;
}
