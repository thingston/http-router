<?php

declare(strict_types=1);

namespace Thingston\Http\Router;

use FastRoute\RouteParser\Std;
use Psr\Http\Server\RequestHandlerInterface;

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
     * @return array<string, string|null>
     */
    private function parseParameters(): array
    {
        $parameters = [];

        /** @var array<string>|string $segment */
        foreach ((new Std())->parse($this->pattern)[0] as $segment) {
            if (is_string($segment)) {
                continue;
            }

            $parameters[$segment[0]] = null;
        }

        return $parameters;
    }
}
