<?php

declare(strict_types=1);

namespace Thingston\Http\Router;

use FastRoute\RouteParser\Std;
use Psr\Http\Server\RequestHandlerInterface;
use Thingston\Http\Router\Exception\InvalidArgumentException;

class Route implements RouteInterface
{
    /**
     * @var RequestHandlerInterface|callable|string
     */
    private $handler;

    /**
     * @var array<string, string|null>|null
     */
    private ?array $parameters = null;

    /**
     * @param array<string> $methods
     * @param string $pattern
     * @param string $name
     * @param RequestHandlerInterface|callable|string $handler
     */
    public function __construct(
        private array $methods,
        private string $pattern,
        private string $name,
        RequestHandlerInterface|callable|string $handler
    ) {
        $this->methods = $methods;
        $this->pattern = $pattern;
        $this->name = $name;
        $this->handler = $handler;
    }

    /**
     * @return array<string>
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHandler(): RequestHandlerInterface|callable|string
    {
        return $this->handler;
    }

    /**
     * @return array<string, string|null>
     */
    public function getParameters(): array
    {
        if (null === $this->parameters) {
            $this->parameters = $this->parseParameters();
        }

        return $this->parameters;
    }

    /**
     * @param array<string, string>|null $candidates
     * @return array<string, string|null>
     */
    private function parseParameters(?array $candidates = null): array
    {
        $parameters = [];

        /** @var array<array<array<string>|string>> $matches */
        $matches = (new Std())->parse($this->pattern);

        $optionals = false;

        /** @var array<array<string>|string> $match */
        foreach ($matches as $match) {
            foreach ($match as $segment) {
                if (is_string($segment)) {
                    continue;
                }

                $name = $segment[0];

                if (is_array($candidates) && false === $optionals && false === isset($candidates[$name])) {
                    throw InvalidArgumentException::forMissingParameter($name);
                }

                $parameters[$name] = $candidates[$name] ?? null;
            }

            $optionals = true;
        }

        return $parameters;
    }

    /**
     * @param array<string, string> $parameters
     * @return self
     */
    public function withParameters(array $parameters): self
    {
        $route = clone $this;
        $route->parameters = $this->parseParameters($parameters);

        return $route;
    }
}
