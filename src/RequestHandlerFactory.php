<?php

declare(strict_types=1);

namespace Thingston\Http\Router;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandlerFactory implements RequestHandlerFactoryInterface
{
    /**
     * @param callable $callable
     * @param array<string, string|null> $arguments
     * @return RequestHandlerInterface
     */
    public static function create(callable $callable, array $arguments = []): RequestHandlerInterface
    {
        return new class ($callable, $arguments) implements RequestHandlerInterface
        {
            /**
             * @var callable
             */
            private $callable;

            /**
             * @param callable $callable
             * @param array<string, string|null> $arguments
             */
            public function __construct(callable $callable, private array $arguments)
            {
                $this->callable = $callable;
                $this->arguments = $arguments;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $callable = $this->callable;

                return $callable($request, ...$this->arguments);
            }
        };
    }
}
