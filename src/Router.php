<?php

declare(strict_types=1);

namespace Thingston\Http\Router;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Thingston\Http\Exception\MethodNotAllowedException;
use Thingston\Http\Exception\NotFoundException;

use function FastRoute\simpleDispatcher;

class Router implements RouterInterface
{
    private RouteCollectionInterface $routes;

    public function __construct(?RouteCollectionInterface $routes = null)
    {
        $this->routes = $routes ?? new RouteCollection();
    }

    public function addRoute(RouteInterface $route): self
    {
        $this->routes->add($route);

        return $this;
    }

    public function getRoutes(): RouteCollectionInterface
    {
        return $this->routes;
    }

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
    ): RouteInterface {
        $route = new Route($methods, $pattern, $name, $handler);
        $this->routes->add($route);

        return $route;
    }

    public function get(
        string $pattern,
        string $name,
        RequestHandlerInterface|callable|string $handler
    ): RouteInterface {
        return $this->map(['GET'], $pattern, $name, $handler);
    }

    public function head(
        string $pattern,
        string $name,
        RequestHandlerInterface|callable|string $handler
    ): RouteInterface {
        return $this->map(['HEAD'], $pattern, $name, $handler);
    }

    public function post(
        string $pattern,
        string $name,
        RequestHandlerInterface|callable|string $handler
    ): RouteInterface {
        return $this->map(['POST'], $pattern, $name, $handler);
    }

    public function put(
        string $pattern,
        string $name,
        RequestHandlerInterface|callable|string $handler
    ): RouteInterface {
        return $this->map(['PUT'], $pattern, $name, $handler);
    }

    public function patch(
        string $pattern,
        string $name,
        RequestHandlerInterface|callable|string $handler
    ): RouteInterface {
        return $this->map(['PATCH'], $pattern, $name, $handler);
    }

    public function delete(
        string $pattern,
        string $name,
        RequestHandlerInterface|callable|string $handler
    ): RouteInterface {
        return $this->map(['DELETE'], $pattern, $name, $handler);
    }

    public function options(
        string $pattern,
        string $name,
        RequestHandlerInterface|callable|string $handler
    ): RouteInterface {
        return $this->map(['OPTIONS'], $pattern, $name, $handler);
    }

    public function match(ServerRequestInterface $request): RouteInterface
    {
        $dispatcher = simpleDispatcher(function (RouteCollector $r) {
            /** @var RouteInterface $route */
            foreach ($this->routes as $name => $route) {
                $r->addRoute($route->getMethods(), $route->getPattern(), $name);
            }
        });

        $uri = $request->getUri()->getPath();
        $result = $dispatcher->dispatch($request->getMethod(), $uri);

        switch ($result[0]) {
            case Dispatcher::FOUND:
                break;
            case Dispatcher::NOT_FOUND:
                throw new NotFoundException(sprintf(
                    'Resource for URL "%s" not found.',
                    (string) $request->getUri()
                ));
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedException(sprintf(
                    'Method "%s" is not allowed for URL "%s"; please use "%s" instead.',
                    $request->getMethod(),
                    (string) $request->getUri(),
                    implode('", "', $result[1])
                ));
        }

        return $this->routes->get($result[1]);
    }
}
