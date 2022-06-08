<?php

declare(strict_types=1);

namespace Thingston\Tests\Http\Router;

use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Thingston\Http\Router\Route;

final class RouteTest extends TestCase
{
    public function testRoute(): void
    {
        $methods = ['GET', 'HEAD'];
        $pattern = '/';
        $name = 'home';
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();

        $route = new Route($methods, $pattern, $name, $handler);

        $this->assertSame($methods, $route->getMethods());
        $this->assertSame($pattern, $route->getPattern());
        $this->assertSame($name, $route->getName());
        $this->assertSame($handler, $route->getHandler());
    }
}
