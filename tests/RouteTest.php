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

    public function testArgumentMethodsCanBeString(): void
    {
        $route = new Route('GET', '/', 'home', 'handler');

        $this->assertSame(['GET'], $route->getMethods());
    }

    public function testArgumentMethodsCantBeEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Route([], '/', 'home', 'handler');
    }

    public function testBadPatternArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Route('GET', '/hello/{name}]', 'home', 'handler');
    }

    /**
     * @dataProvider getUrlProvider
     * @param string $url
     * @param string $pattern
     * @param array<string, string> $parameters
     * @param array<string, string> $query
     * @param string $hostname
     * @return void
     */
    public function testGetUrl(string $url, string $pattern, array $parameters, array $query, string $hostname): void
    {
        $route = new Route('GET', $pattern, 'home', 'handler');

        $this->assertSame($url, $route->getUrl($parameters, $query, $hostname));
    }

    /**
     * @return array<array<mixed>>
     */
    public function getUrlProvider(): array
    {
        return [
            ['/', '/', [], [], ''],
            ['/?foo=bar', '/', [], ['foo' => 'bar'], ''],
            ['/hello/pedro', '/hello/{name}', ['name' => 'pedro'], [], ''],
            ['/hello/pedro', '/hello/{name}[/in/{place}]', ['name' => 'pedro'], [], ''],
            ['/hello/pedro/in/lisbon', '/hello/{name}[/in/{place}]', ['name' => 'pedro', 'place' => 'lisbon'], [], ''],
            ['http://example.org/', '/', [], [], 'http://example.org'],
            ['http://example.org/?foo=bar', '/', [], ['foo' => 'bar'], 'http://example.org'],
        ];
    }
}
