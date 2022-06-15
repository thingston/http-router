<?php

declare(strict_types=1);

namespace Thingston\Tests\Http\Router;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TestClassMethod
{
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        return new Response();
    }
}
