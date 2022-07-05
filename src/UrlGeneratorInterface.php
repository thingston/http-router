<?php

declare(strict_types=1);

namespace Thingston\Http\Router;

interface UrlGeneratorInterface
{
    /**
     * @param string $name
     * @param array<string, string> $parameters
     * @param array<string, string> $query
     * @return string
     */
    public function generate(string $name, array $parameters = [], array $query = []): string;
}
