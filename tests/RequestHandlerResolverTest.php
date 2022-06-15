<?php

declare(strict_types=1);

namespace Thingston\Tests\Http\Router;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Thingston\Http\Router\Exception\InvalidArgumentException;
use Thingston\Http\Router\RequestHandlerResolver;
use Thingston\Http\Router\Route;

final class RequestHandlerResolverTest extends TestCase
{
    public function testRequestHandler(): void
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();

        $resolver = new RequestHandlerResolver();
        $route = new Route(['GET'], '/', 'home', $handler);

        $this->assertSame($handler, $resolver->resolve($route));
    }

    public function testCallable(): void
    {
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();

        $handler = function (ServerRequestInterface $request) use ($response) {
            return $response;
        };

        $resolver = new RequestHandlerResolver();
        $route = new Route(['GET'], '/', 'home', $handler);

        $resolved = $resolver->resolve($route);

        $this->assertInstanceOf(RequestHandlerInterface::class, $resolved);

        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $this->assertSame($response, $resolved->handle($request));
    }

    public function testContainerReturnsRequestHandler(): void
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();

        $container = $this->createConfiguredMock(ContainerInterface::class, [
            'has' => true,
            'get' => $handler,
        ]);

        $resolver = new RequestHandlerResolver($container);
        $route = new Route(['GET'], '/', 'home', 'handler');

        $this->assertSame($handler, $resolver->resolve($route));
    }

    public function testContainerReturnsCallable(): void
    {
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();

        $handler = function (ServerRequestInterface $request) use ($response) {
            return $response;
        };

        $container = $this->createConfiguredMock(ContainerInterface::class, [
            'has' => true,
            'get' => $handler,
        ]);

        $resolver = new RequestHandlerResolver($container);
        $route = new Route(['GET'], '/', 'home', 'handler');

        $resolved = $resolver->resolve($route);

        $this->assertInstanceOf(RequestHandlerInterface::class, $resolved);

        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $this->assertSame($response, $resolved->handle($request));
    }

    public function testContainerReturnsInvalidHandler(): void
    {
        $handler = 'foo';

        $container = $this->createConfiguredMock(ContainerInterface::class, [
            'has' => true,
            'get' => $handler,
        ]);

        $resolver = new RequestHandlerResolver($container);
        $route = new Route(['GET'], '/', 'home', 'handler');

        $this->expectException(InvalidArgumentException::class);
        $resolver->resolve($route);
    }

    public function testClassIsRequestHandler(): void
    {
        $resolver = new RequestHandlerResolver();
        $route = new Route(['GET'], '/', 'home', TestRequestHandler::class);

        $this->assertInstanceOf(TestRequestHandler::class, $resolver->resolve($route));
    }

    public function testClassIsInvokable(): void
    {
        $resolver = new RequestHandlerResolver();
        $route = new Route(['GET'], '/', 'home', TestInvokable::class);

        $resolved = $resolver->resolve($route);

        $this->assertInstanceOf(RequestHandlerInterface::class, $resolved);

        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $this->assertInstanceOf(ResponseInterface::class, $resolved->handle($request));
    }

    public function testClassMethodFromContainer(): void
    {
        $handler = new TestClassMethod();

        $container = $this->createConfiguredMock(ContainerInterface::class, [
            'has' => true,
            'get' => $handler,
        ]);

        $resolver = new RequestHandlerResolver($container);
        $route = new Route(['GET'], '/', 'home', TestClassMethod::class . '@index');

        $resolved = $resolver->resolve($route);

        $this->assertInstanceOf(RequestHandlerInterface::class, $resolved);

        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $this->assertInstanceOf(ResponseInterface::class, $resolved->handle($request));
    }

    public function testClassMethod(): void
    {
        $resolver = new RequestHandlerResolver();
        $route = new Route(['GET'], '/', 'home', TestClassMethod::class . '@index');

        $resolved = $resolver->resolve($route);

        $this->assertInstanceOf(RequestHandlerInterface::class, $resolved);

        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $this->assertInstanceOf(ResponseInterface::class, $resolved->handle($request));
    }

    public function testUnableToResolve(): void
    {
        $resolver = new RequestHandlerResolver();
        $route = new Route(['GET'], '/', 'home', 'handler');

        $this->expectException(InvalidArgumentException::class);
        $resolver->resolve($route);
    }
}
