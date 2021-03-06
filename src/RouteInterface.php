<?php

declare(strict_types=1);

namespace Thingston\Http\Router;

use Psr\Http\Server\MiddlewareInterface;
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

    /**
     * @param array<string, string> $parameters
     * @param array<string, string> $query
     * @param string $hostname
     * @return string
     */
    public function getUrl(array $parameters = [], array $query = [], string $hostname = ''): string;

    /**
     * @return array<MiddlewareInterface>
     */
    public function getMiddlewares(): array;
    public function pipe(MiddlewareInterface $middleware): self;
}
