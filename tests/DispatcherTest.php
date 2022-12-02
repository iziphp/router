<?php

namespace PhpStandard\HttpServerHandler\Tests;

use Laminas\Diactoros\ServerRequestFactory;
use PhpStandard\Container\Configurator;
use PhpStandard\Container\Container;
use PhpStandard\Router\Dispatcher;
use PhpStandard\Router\Exceptions\RouteNotFoundException;
use PhpStandard\Router\RouteCollector;
use PhpStandard\Router\RouteGroup;
use PhpStandard\Router\Tests\Mock\MockRequestHandler;
use PhpStandard\Router\Tests\Mock\MockSimpleMiddleware;
use PHPUnit\Framework\TestCase;

class DispatcherTest extends TestCase
{
    private Dispatcher $dispatcher;
    private ServerRequestFactory $factory;

    protected function setUp(): void
    {
        $rc = new RouteCollector([MockSimpleMiddleware::class]);

        $group = new RouteGroup(
            '/foo',
            'foo-name',
            [new MockSimpleMiddleware('X-GROUP-FOO', 'foo')]
        );

        $subgroup = (new RouteGroup(
            '/bar',
            'bar-name',
            [new MockSimpleMiddleware('X-GROUP-BAR', 'bar')]
        ))->map(
            'GET',
            '/baz',
            MockRequestHandler::class,
            'baz-name',
            [new MockSimpleMiddleware('X-ROUTE-BAZ', 'baz')]
        );

        $rc->add($group);
        $group->add($subgroup);

        $rc->add(
            (new RouteGroup(
                '/qux',
                'qux-name',
                [new MockSimpleMiddleware('X-GROUP-QUX', 'qux')]
            ))->map(
                'GET',
                '/quux',
                MockRequestHandler::class,
                'quux-name',
                [new MockSimpleMiddleware('X-ROUTE-QUUX', 'quux')]
            )
        )
            ->map(
                'GET',
                '/quuz',
                MockRequestHandler::class,
                'quuz-name',
                [new MockSimpleMiddleware('X-ROUTE-QUUZ', 'quuz')]
            );

        $this->dispatcher = new Dispatcher(
            $rc,
            new Container(new Configurator())
        );

        $this->factory = new ServerRequestFactory();
    }

    /** @test */
    public function canDispatch()
    {
        $request = $this->factory->createServerRequest(
            'GET',
            '/foo/bar/baz'
        );

        $route = $this->dispatcher->dispatch($request);
        $iterator = $route->getIterator();

        $this->assertEquals('baz-name', $route->getName());
        $this->assertEquals(5, $iterator->count());
        $this->assertInstanceOf(
            MockRequestHandler::class,
            $iterator[$iterator->count() - 1]
        );

        $legs = [...$iterator];
        $this->assertCount(5, $legs);
    }

    /** @test */
    public function canThrowRouteNotFoundException()
    {
        $request = $this->factory->createServerRequest(
            'GET',
            '/foo/bar/quux'
        );

        $this->expectException(RouteNotFoundException::class);
        $this->dispatcher->dispatch($request);
    }
}
