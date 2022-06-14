<?php

declare(strict_types=1);

namespace Thingston\Tests\Http\Router;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Thingston\Http\Router\Route;

final class RouteTest extends TestCase
{
    /**
     * @dataProvider routeProvider
     *
     * @param array<string> $methods
     * @param string $pattern
     * @param string $name
     * @param RequestHandlerInterface|callable|string $handler
     */
    public function testRoute(
        array $methods,
        string $pattern,
        string $name,
        RequestHandlerInterface|callable|string $handler
    ): void {
        $route = new Route($methods, $pattern, $name, $handler);

        $this->assertSame($methods, $route->getMethods());
        $this->assertSame($pattern, $route->getPattern());
        $this->assertSame($name, $route->getName());
        $this->assertSame($handler, $route->getHandler());
    }

    /**
     * @return array<array<mixed>>
     */
    public function routeProvider(): array
    {
        return [
            [['GET'], '/', 'home', $this->getMockBuilder(RequestHandlerInterface::class)->getMock()],
            [['GET'], '/', 'home', function (): ResponseInterface {
                return $this->getMockBuilder(ResponseInterface::class)->getMock();
            }],
            [['GET'], '/', 'home', [$this, 'handlerCallable']],
            [['GET'], '/', 'home', 'handlerCallable'],
        ];
    }

    public function handlerCallable(): ResponseInterface
    {
        return $this->getMockBuilder(ResponseInterface::class)->getMock();
    }
}
