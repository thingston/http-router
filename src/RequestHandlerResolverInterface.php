<?php

declare(strict_types=1);

namespace Thingston\Http\Router;

use Psr\Http\Server\RequestHandlerInterface;

interface RequestHandlerResolverInterface
{
    public function resolve(RouteInterface $route): RequestHandlerInterface;
}
