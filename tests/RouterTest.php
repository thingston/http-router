<?php

declare(strict_types=1);

namespace Thingston\Tests\Http\Router;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Thingston\Http\Exception\MethodNotAllowedException;
use Thingston\Http\Exception\NotFoundException;
use Thingston\Http\Router\Route;
use Thingston\Http\Router\Router;

final class RouterTest extends TestCase
{
    public function testRouteCollection(): void
    {
        $router = new Router();

        $router->addRoute(new Route(['GET'], '/', 'home', 'HomeAction'))
            ->addRoute(new Route(['GET'], '/about', 'about', 'AboutAction'))
            ->addRoute(new Route(['GET'], '/hello/{name}', 'hello', 'HelloAction'));

        $this->assertCount(3, $router->getRoutes());
    }

    /**
     * @dataProvider matchRouteProvider
     *
     * @param string $pattern
     * @param string $name
     * @param string $path
     * @param array<string, string> $parameters
     */
    public function testMatchRoute(string $pattern, string $name, string $path, array $parameters): void
    {
        $handler = $name . 'Action';

        $router = new Router();

        $router
            ->addRoute(new Route(['GET'], '/pattern1', 'name1', 'handler1'))
            ->addRoute(new Route(['GET'], '/pattern2', 'name2', 'handler2'))
            ->addRoute(new Route(['GET'], $pattern, $name, $handler))
            ->addRoute(new Route(['GET'], '/pattern3', 'name3', 'handler3'))
            ;

        $request = new ServerRequest('GET', 'http://example.org' . $path);
        $route = $router->match($request);

        $this->assertSame(['GET'], $route->getMethods());
        $this->assertSame($pattern, $route->getPattern());
        $this->assertSame($name, $route->getName());
        $this->assertSame($handler, $route->getHandler());
        $this->assertSame($parameters, $route->getParameters());
    }

    /**
     * @return array<array<mixed>>
     */
    public function matchRouteProvider(): array
    {
        return [
            ['/', 'home', '/', []],
            ['/about', 'about', '/about', []],
            ['/hello/{name}', 'hello', '/hello/foo', ['name' => 'foo']],
            ['/hello/{name}/{required}', 'hello', '/hello/foo/bar', ['name' => 'foo', 'required' => 'bar']],
            ['/hello/{name}[/{optional}]', 'hello', '/hello/foo/bar', ['name' => 'foo', 'optional' => 'bar']],
            ['/hello/{name}[/{optional}]', 'hello', '/hello/foo', ['name' => 'foo', 'optional' => null]],
        ];
    }

    public function testNotFoundException(): void
    {
        $router = new Router();

        $request = new ServerRequest('GET', 'http://example.org/');

        $this->expectException(NotFoundException::class);
        $router->match($request);
    }

    public function testMethodNotAllowedException(): void
    {
        $router = new Router();

        $router->addRoute(new Route(['GET'], '/', 'home', 'HomeAction'));

        $request = new ServerRequest('POST', 'http://example.org/');

        $this->expectException(MethodNotAllowedException::class);
        $router->match($request);
    }

    public function testMapRoute(): void
    {
        $router = new Router();

        $route = $router->map(['GET'], '/', 'home', 'HomeAction');

        $this->assertSame(['GET'], $route->getMethods());
        $this->assertSame('/', $route->getPattern());
        $this->assertSame('home', $route->getName());
        $this->assertSame('HomeAction', $route->getHandler());
    }

    public function testGetRoute(): void
    {
        $router = new Router();

        $route = $router->get('/', 'home', 'HomeAction');

        $this->assertSame(['GET'], $route->getMethods());
        $this->assertSame('/', $route->getPattern());
        $this->assertSame('home', $route->getName());
        $this->assertSame('HomeAction', $route->getHandler());
    }

    public function testHeadRoute(): void
    {
        $router = new Router();

        $route = $router->head('/', 'home', 'HomeAction');

        $this->assertSame(['HEAD'], $route->getMethods());
        $this->assertSame('/', $route->getPattern());
        $this->assertSame('home', $route->getName());
        $this->assertSame('HomeAction', $route->getHandler());
    }

    public function testPostRoute(): void
    {
        $router = new Router();

        $route = $router->post('/', 'home', 'HomeAction');

        $this->assertSame(['POST'], $route->getMethods());
        $this->assertSame('/', $route->getPattern());
        $this->assertSame('home', $route->getName());
        $this->assertSame('HomeAction', $route->getHandler());
    }

    public function testPutRoute(): void
    {
        $router = new Router();

        $route = $router->put('/', 'home', 'HomeAction');

        $this->assertSame(['PUT'], $route->getMethods());
        $this->assertSame('/', $route->getPattern());
        $this->assertSame('home', $route->getName());
        $this->assertSame('HomeAction', $route->getHandler());
    }

    public function testPatchRoute(): void
    {
        $router = new Router();

        $route = $router->patch('/', 'home', 'HomeAction');

        $this->assertSame(['PATCH'], $route->getMethods());
        $this->assertSame('/', $route->getPattern());
        $this->assertSame('home', $route->getName());
        $this->assertSame('HomeAction', $route->getHandler());
    }

    public function testDeleteRoute(): void
    {
        $router = new Router();

        $route = $router->delete('/', 'home', 'HomeAction');

        $this->assertSame(['DELETE'], $route->getMethods());
        $this->assertSame('/', $route->getPattern());
        $this->assertSame('home', $route->getName());
        $this->assertSame('HomeAction', $route->getHandler());
    }

    public function testOptionsRoute(): void
    {
        $router = new Router();

        $route = $router->options('/', 'home', 'HomeAction');

        $this->assertSame(['OPTIONS'], $route->getMethods());
        $this->assertSame('/', $route->getPattern());
        $this->assertSame('home', $route->getName());
        $this->assertSame('HomeAction', $route->getHandler());
    }
}
