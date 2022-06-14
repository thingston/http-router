<?php

declare(strict_types=1);

namespace Thingston\Http\Router;

use ArrayObject;
use Thingston\Http\Router\Exception\InvalidArgumentException;
use Traversable;

use function count;

class RouteCollection implements RouteCollectionInterface
{
    /**
     * @param array<string, RouteInterface> $routes
     */
    public function __construct(private array $routes = [])
    {
        $this->routes = $routes;
    }

    public function add(RouteInterface $route): RouteCollectionInterface
    {
        $this->routes[$route->getName()] = $route;

        return $this;
    }

    public function get(string $name): RouteInterface
    {
        if (false === isset($this->routes[$name])) {
            throw InvalidArgumentException::forInvalidRouteName($name);
        }

        return $this->routes[$name];
    }

    public function count(): int
    {
        return count($this->routes);
    }

    /**
     * @return Traversable<string, RouteInterface>
     */
    public function getIterator(): Traversable
    {
        return new ArrayObject($this->routes);
    }
}
