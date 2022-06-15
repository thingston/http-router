<?php

declare(strict_types=1);

namespace Thingston\Http\Router\Exception;

class InvalidArgumentException extends \InvalidArgumentException implements RouterExceptionInterface
{
    public static function forInvalidRouteName(string $name): self
    {
        return new self(sprintf('Route named "%s" not found.', $name));
    }

    public static function forMissingParameter(string $name): self
    {
        return new self(sprintf('Missing required parameter "%s".', $name));
    }
}
