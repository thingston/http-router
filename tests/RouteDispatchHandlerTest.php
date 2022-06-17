<?php

declare(strict_types=1);

namespace Thingston\Tests\Http\Router;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Thingston\Http\Router\RequestHandlerResolver;
use Thingston\Http\Router\Route;
use Thingston\Http\Router\RouteDispatchHandler;

final class RouteDispatchHandlerTest extends TestCase
{
    public function testWithoutMiddlewares(): void
    {
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $handler = $this->createConfiguredMock(RequestHandlerInterface::class, [
            'handle' => $response,
        ]);
        $route = new Route(['GET'], '/', 'home', $handler);
        $resolver = new RequestHandlerResolver();

        $dispatcher = new RouteDispatchHandler($resolver, $route);
        $request = new ServerRequest('GET', 'http://example.org');

        $this->assertSame($response, $dispatcher->handle($request));
    }

    public function testWithMiddlewares(): void
    {
        $handler = $this->createConfiguredMock(RequestHandlerInterface::class, [
            'handle' => new Response(),
        ]);

        $route = new Route(['GET'], '/', 'home', $handler);
        $route->pipe(new TestMiddleware('R1'))->pipe(new TestMiddleware('R2'));

        $resolver = new RequestHandlerResolver();

        $dispatcher = new RouteDispatchHandler($resolver, $route, [
            new TestMiddleware('A1'),
            new TestMiddleware('A2'),
            new TestMiddleware('A3'),
        ]);

        $request = new ServerRequest('GET', 'http://example.org');
        $response = $dispatcher->handle($request);

        $this->assertSame('R2R1A3A2A1', $response->getBody()->getContents());
    }
}
