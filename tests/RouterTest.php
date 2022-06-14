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

    public function testMatchRoute(): void
    {
        $router = new Router();

        $router->addRoute(new Route(['GET'], '/', 'home', 'HomeAction'))
            ->addRoute(new Route(['GET'], '/about', 'about', 'AboutAction'))
            ->addRoute(new Route(['GET'], '/hello/{name}', 'hello', 'HelloAction'));

        $request = new ServerRequest('GET', 'http://example.org/about');
        $route = $router->match($request);

        $this->assertSame(['GET'], $route->getMethods());
        $this->assertSame('/about', $route->getPattern());
        $this->assertSame('about', $route->getName());
        $this->assertSame('AboutAction', $route->getHandler());
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
