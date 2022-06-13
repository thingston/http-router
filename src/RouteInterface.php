<?php

declare(strict_types=1);

namespace Thingston\Http\Router;

use Psr\Http\Server\RequestHandlerInterface;

interface RouteInterface
{
    /**
     * @return array<string>
     */
    public function getMethods(): array;

    public function getPattern(): string;
    public function getName(): string;
    public function getHandler(): RequestHandlerInterface|callable|string;
}
