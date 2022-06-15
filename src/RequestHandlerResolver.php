<?php

declare(strict_types=1);

namespace Thingston\Http\Router;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Thingston\Http\Router\Exception\InvalidArgumentException;

class RequestHandlerResolver implements RequestHandlerResolverInterface
{
    public function __construct(private ?ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function resolve(RouteInterface $route): RequestHandlerInterface
    {
        $handler = $route->getHandler();

        if ($handler instanceof RequestHandlerInterface) {
            return $handler;
        }

        if (is_callable($handler)) {
            return RequestHandlerFactory::create($handler, $route->getParameters());
        }

        if (strpos($handler, '@')) {
            $parts = explode('@', $handler, 2);

            if ($this->container && $this->container->has($parts[0])) {
                $instance = $this->container->get($parts[0]);
            } elseif (class_exists($parts[0])) {
                $instance = new $parts[0]();
            }

            $method = $parts[1] ?? false;

            if ($method && isset($instance) && is_object($instance) && method_exists($instance, $method)) {
                return $this->resolveInstance([$instance, $method], $route);
            }
        }

        if ($this->container && $this->container->has($handler)) {
            return $this->resolveInstance($this->container->get($handler), $route);
        }

        if (class_exists($handler)) {
            return $this->resolveInstance(new $handler(), $route);
        }

        throw InvalidArgumentException::forInvalidHandler($handler);
    }

    /**
     * @param mixed $instance
     * @param RouteInterface $route
     * @return RequestHandlerInterface
     */
    private function resolveInstance($instance, RouteInterface $route): RequestHandlerInterface
    {
        if ($instance instanceof RequestHandlerInterface) {
            return $instance;
        }

        if (is_callable($instance)) {
            return RequestHandlerFactory::create($instance, $route->getParameters());
        }

        $name = is_string($route->getHandler()) ? $route->getHandler() : gettype($route->getHandler());
        throw InvalidArgumentException::forInvalidHandler($name);
    }
}
