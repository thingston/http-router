<?php

declare(strict_types=1);

namespace Thingston\Tests\Http\Router;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TestInvokable
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return new Response();
    }
}
