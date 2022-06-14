<?php

declare(strict_types=1);

namespace Thingston\Http\Router;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    public function addRoute(RouteInterface $route): self;
    public function getRoutes(): RouteCollectionInterface;

    /**
     * @param array<string> $methods
     * @param string $pattern
     * @param string $name
     * @param RequestHandlerInterface|callable|string $handler
     * @return RouteInterface
     */
    public function map(
        array $methods,
        string $pattern,
        string $name,
        RequestHandlerInterface|callable|string $handler
    ): RouteInterface;

    public function get(
        string $pattern,
        string $name,
        RequestHandlerInterface|callable|string $handler
    ): RouteInterface;

    public function head(
        string $pattern,
        string $name,
        RequestHandlerInterface|callable|string $handler
    ): RouteInterface;

    public function post(
        string $pattern,
        string $name,
        RequestHandlerInterface|callable|string $handler
    ): RouteInterface;

    public function put(
        string $pattern,
        string $name,
        RequestHandlerInterface|callable|string $handler
    ): RouteInterface;

    public function patch(
        string $pattern,
        string $name,
        RequestHandlerInterface|callable|string $handler
    ): RouteInterface;

    public function delete(
        string $pattern,
        string $name,
        RequestHandlerInterface|callable|string $handler
    ): RouteInterface;

    public function options(
        string $pattern,
        string $name,
        RequestHandlerInterface|callable|string $handler
    ): RouteInterface;

    public function match(ServerRequestInterface $request): RouteInterface;
}
