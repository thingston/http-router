<?php

declare(strict_types=1);

namespace Thingston\Tests\Http\Router;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Thingston\Http\Router\Exception\InvalidArgumentException;
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

    public function testRouteParameters(): void
    {
        $route = new Route(['GET'], '/hello/{name}/in/{location}/{date}', 'home', 'handler');

        $this->assertSame([
            'name' => null,
            'location' => null,
            'date' => null,
        ], $route->getParameters());
    }

    public function testRouteWithParameters(): void
    {
        $parameters = [
            'name' => 'foo',
            'location' => 'bar',
            'date' => 'baz',
        ];

        $route = (new Route(['GET'], '/hello/{name}/in/{location}/{date}', 'home', 'handler'))
            ->withParameters($parameters);

        $this->assertSame($parameters, $route->getParameters());
    }

    public function testRouteWithMissingsParameters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new Route(['GET'], '/hello/{name}/in/{location}/{date}', 'home', 'handler'))
            ->withParameters([]);
    }

    public function testPipeMiddlewares(): void
    {
        $route = new Route(['GET'], '/', 'home', 'handler');
        $route->pipe($this->getMockBuilder(MiddlewareInterface::class)->getMock())
            ->pipe($this->getMockBuilder(MiddlewareInterface::class)->getMock())
            ->pipe($this->getMockBuilder(MiddlewareInterface::class)->getMock());

        $this->assertCount(3, $route->getMiddlewares());
    }
}
