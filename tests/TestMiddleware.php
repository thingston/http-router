<?php

declare(strict_types=1);

namespace Thingston\Tests\Http\Router;

use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TestMiddleware implements MiddlewareInterface
{
    public function __construct(private string $body)
    {
        $this->body = $body;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $body = $response->getBody()->getContents() . $this->body;

        return $response->withBody(Utils::streamFor($body));
    }
}
