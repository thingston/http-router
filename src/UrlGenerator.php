<?php

declare(strict_types=1);

namespace Thingston\Http\Router;

use Psr\Http\Message\UriInterface;

final class UrlGenerator implements UrlGeneratorInterface
{
    public function __construct(private RouterInterface $router, private ?UriInterface $uri = null)
    {
        $this->router = $router;
        $this->uri = $uri;
    }

    /**
     * @param string $name
     * @param array<string, string> $parameters
     * @param array<string, string> $query
     * @return string
     */
    public function generate(string $name, array $parameters = [], array $query = []): string
    {
        $route = $this->router->getRoutes()->get($name);
        $hostname = null === $this->uri ? '' : (string) $this->uri->withPath('')->withQuery('')->withFragment('');

        return $route->getUrl($parameters, $query, $hostname);
    }
}
