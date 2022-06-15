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

    /**
     * @return array<string, string|null>
     */
    public function getParameters(): array;

    /**
     * @param array<string, string|null> $parameters
     * @return self
     */
    public function withParameters(array $parameters): self;
}
