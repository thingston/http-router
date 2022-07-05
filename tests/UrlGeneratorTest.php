<?php

declare(strict_types=1);

namespace Thingston\Tests\Http\Router;

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Thingston\Http\Router\Route;
use Thingston\Http\Router\Router;

final class UrlGeneratorTest extends TestCase
{
    /**
     * @dataProvider generateProvider
     * @param string $url
     * @param string $pattern
     * @param array<string, string> $parameters
     * @param array<string, string> $query
     * @param string $hostname
     * @return void
     */
    public function testGenerate(string $url, string $pattern, array $parameters, array $query, string $hostname): void
    {
        $router = new Router();
        $router->addRoute(new Route('GET', $pattern, 'home', 'handler'));

        $uri = $hostname ? new Uri($hostname) : null;

        $generator = new \Thingston\Http\Router\UrlGenerator($router, $uri);

        $this->assertSame($url, $generator->generate('home', $parameters, $query));
    }

    /**
     * @return array<array<mixed>>
     */
    public function generateProvider(): array
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
