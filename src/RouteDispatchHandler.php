<?php

declare(strict_types=1);

namespace Thingston\Http\Router;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouteDispatchHandler implements RequestHandlerInterface
{
    private RequestHandlerInterface $routeHandler;

    /**
     * @var array<\Psr\Http\Server\MiddlewareInterface>
     */
    private array $routeMiddlewares;

    /**
     * @param RequestHandlerResolverInterface $resolver
     * @param RouteInterface $route
     * @param array<\Psr\Http\Server\MiddlewareInterface> $midlewares
     */
    public function __construct(
        RequestHandlerResolverInterface $resolver,
        private RouteInterface $route,
        private array $midlewares = []
    ) {
        $this->route = $route;
        $this->midlewares = $midlewares;

        $this->routeHandler = $resolver->resolve($route);
        $this->routeMiddlewares = $route->getMiddlewares();
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $name = RouteInterface::class;

        if (null === $request->getAttribute($name)) {
            $request = $request->withAttribute($name, $this->route);
        }

        while ($middleware = array_shift($this->midlewares)) {
            return $middleware->process($request, $this);
        }

        while ($middleware = array_shift($this->routeMiddlewares)) {
            return $middleware->process($request, $this);
        }

        return $this->routeHandler->handle($request);
    }
}
