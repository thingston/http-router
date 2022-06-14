<?php

declare(strict_types=1);

namespace Thingston\Tests\Http\Router;

use PHPUnit\Framework\TestCase;
use Thingston\Http\Router\RouteCollection;
use Thingston\Http\Router\RouteInterface;
use Thingston\Http\Router\Exception\InvalidArgumentException;

final class RouteCollectionTest extends TestCase
{
    public function testCollection(): void
    {
        $collection = new RouteCollection();

        $this->assertCount(0, $collection);

        for ($i = 0; $i < rand(1, 10); $i++) {
            $route = $this->createConfiguredMock(RouteInterface::class, [
                'getName' => 'route' . $i,
            ]);

            $collection->add($route);
        }

        $this->assertCount($i, $collection);

        foreach ($collection as $name => $route) {
            $this->assertSame($route, $collection->get($name));
        }
    }

    public function testInvalidRouteName(): void
    {
        $collection = new RouteCollection();

        $this->expectException(InvalidArgumentException::class);
        $collection->get('foo');
    }
}
