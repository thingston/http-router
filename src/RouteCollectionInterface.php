<?php

declare(strict_types=1);

namespace Thingston\Http\Router;

use Countable;
use IteratorAggregate;

interface RouteCollectionInterface extends Countable, IteratorAggregate
{
    public function add(RouteInterface $route): self;
    public function get(string $name): RouteInterface;
}
