<?php

declare(strict_types=1);

namespace Thingston\Http\Router\Exception;

class InvalidArgumentException extends \InvalidArgumentException implements RouterExceptionInterface
{
    public static function forEmptyMethods(): self
    {
        return new self('Argument "$methods" can\'t be empty.');
    }

    public static function forInvalidRouteName(string $name): self
    {
        return new self(sprintf('Route named "%s" not found.', $name));
    }

    public static function forMissingParameter(string $name): self
    {
        return new self(sprintf('Missing required parameter "%s".', $name));
    }

    public static function forInvalidHandler(string $name): self
    {
        return new self(sprintf('Unable to resolve handler "%s".', $name));
    }
}
