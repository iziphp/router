<?php

declare(strict_types=1);

namespace PhpStandard\Router\Tests;

use PhpStandard\Router\Route;
use PhpStandard\Router\RouteGroup;
use PHPUnit\Framework\TestCase;

class RouteGroupTest extends TestCase
{
    private RouteGroup $group;

    protected function setUp(): void
    {
        $this->group = new RouteGroup(
            '/foo',
            'foo-name',
            ['foo-middleware']
        );
    }

    /** @test */
    public function canGetPrefix()
    {
        $this->assertEquals('/foo', $this->group->getPrefix());
    }

    /** @test */
    public function canGetName()
    {
        $this->assertEquals('foo-name', $this->group->getName());
    }

    /** @test */
    public function canGetMiddlewares()
    {
        $this->assertEquals(
            ['foo-middleware'],
            $this->group->getMiddlewareStack()
        );
    }

    /** @test */
    public function canCloneWithAppendedMiddleware()
    {
        $group = $this->group
            ->withAppendedMiddleware('bar-middleware')
            ->withAppendedMiddleware('baz-middleware');
        $this->assertEquals(
            ['foo-middleware'],
            (array)$this->group->getMiddlewareStack()
        );
        $this->assertEquals(
            ['foo-middleware', 'bar-middleware', 'baz-middleware'],
            (array)$group->getMiddlewareStack()
        );
    }

    /**  @test */
    public function canCloneWithPrependedMiddleware()
    {
        $group = $this->group
            ->withPrependedMiddleware('bar-middleware', 'qux-middleware')
            ->withPrependedMiddleware('baz-middleware');
        $this->assertEquals(
            ['foo-middleware'],
            (array)$this->group->getMiddlewareStack()
        );
        $this->assertEquals(
            [
                'baz-middleware',
                'bar-middleware',
                'qux-middleware',
                'foo-middleware'
            ],
            (array)$group->getMiddlewareStack()
        );
    }

    /** @test */
    public function canAddGroup()
    {
        $this->group->add(
            new RouteGroup(
                '/bar',
                'bar-name',
                ['bar-middleware']
            )
        );

        $this->assertInstanceOf(
            RouteGroup::class,
            $this->group->getByName('bar-name')
        );
    }

    /** @test */
    public function canAddRoute()
    {
        $this->group->add(
            new Route(
                'GET',
                '/bar',
                'bar-handler',
                'bar-name',
                ['bar-middleware']
            )
        );

        $this->assertInstanceOf(
            Route::class,
            $this->group->getByName('bar-name')
        );
    }

    /** @test */
    public function canAddNestedGroup()
    {
        $this->group->add(
            new RouteGroup(
                '/bar',
                'bar-name',
                ['bar-middleware']
            )
        );

        $this->group->getByName('bar-name')->add(
            new RouteGroup(
                '/baz',
                'baz-name',
                ['baz-middleware']
            )
        );

        $this->assertInstanceOf(
            RouteGroup::class,
            $this->group->getByName('bar-name')->getByName('baz-name')
        );
    }

    /** @test */
    public function canAddNestedRoute()
    {
        $this->group->add(
            new RouteGroup(
                '/bar',
                'bar-name',
                ['bar-middleware']
            )
        );

        $this->group->getByName('bar-name')->add(
            new Route(
                'GET',
                '/baz',
                'baz-handler',
                'baz-name',
                ['baz-middleware']
            )
        );

        $this->assertInstanceOf(
            Route::class,
            $this->group->getByName('bar-name')->getByName('baz-name')
        );
    }

    /** @test */
    public function canGetByName()
    {
        $this->group->add(
            new RouteGroup(
                '/bar',
                'bar-name',
                ['bar-middleware']
            )
        );

        $this->group->add(
            new Route(
                'GET',
                '/baz',
                'baz-handler',
                'baz-name',
                ['baz-middleware']
            )
        );

        $this->assertInstanceOf(
            RouteGroup::class,
            $this->group->getByName('bar-name')
        );

        $this->assertInstanceOf(
            Route::class,
            $this->group->getByName('baz-name')
        );
    }

    /** @test */
    public function canMap()
    {
        $this->group
            ->add(
                new RouteGroup(
                    '/bar',
                    'bar-name',
                    ['bar-middleware']
                )
            )->add(
                new Route(
                    'GET',
                    '/baz',
                    'baz-handler',
                    'baz-name',
                    ['baz-middleware']
                )
            );

        $this->group
            ->map(
                'GET',
                '/qux',
                'qux-handler',
                'qux-name',
                ['qux-middleware']
            )->map(
                'PUT',
                '/quux',
                'quux-handler',
                'quux-name',
                ['quux-middleware']
            );


        $this->assertInstanceOf(
            Route::class,
            $this->group->getByName('qux-name')
        );

        $this->assertInstanceOf(
            Route::class,
            $this->group->getByName('quux-name')
        );
    }

    /** @test */
    public function canGetRoutes()
    {
        $this->group
            ->add(
                new RouteGroup(
                    '/bar',
                    'bar-name',
                    ['bar-middleware']
                )
            )->add(
                new Route(
                    'GET',
                    '/baz',
                    'baz-handler',
                    'baz-name',
                    ['baz-middleware']
                )
            );

        $this->group
            ->map(
                'GET',
                '/qux',
                'qux-handler',
                'qux-name',
                ['qux-middleware']
            )->map(
                'PUT',
                '/quux',
                'quux-handler',
                'quux-name',
                ['quux-middleware']
            );

        $this->assertCount(3, $this->group->getIterator());
    }

    /** @test */
    public function canGetRoutesWithNestedGroups()
    {
        $this->group
            ->add(
                new RouteGroup(
                    '/bar',
                    'bar-name',
                    ['bar-middleware']
                )
            )->add(
                new Route(
                    'GET',
                    '/baz',
                    'baz-handler',
                    'baz-name',
                    ['baz-middleware']
                )
            );

        $this->group
            ->map(
                'GET',
                '/qux',
                'qux-handler',
                'qux-name',
                ['qux-middleware']
            )->map(
                'PUT',
                '/quux',
                'quux-handler',
                'quux-name',
                ['quux-middleware']
            );

        $this->group->getByName('bar-name')
            ->map(
                'GET',
                '/quuz',
                'quuz-handler',
                'quuz-name',
                ['quuz-middleware']
            )->map(
                'PUT',
                '/corge',
                'corge-handler',
                'corge-name',
                ['corge-middleware']
            );

        $this->assertCount(5, $this->group->getIterator());
    }

    /** @test */
    public function canGetRoutesWithNestedGroupsAndPrependGroupMiddlewares()
    {
        $this->group
            ->add(
                new RouteGroup(
                    '/bar',
                    'bar-name',
                    ['bar-middleware']
                )
            )->add(
                new Route(
                    'GET',
                    '/baz',
                    'baz-handler',
                    'baz-name',
                    ['baz-middleware']
                )
            );

        $this->group
            ->map(
                'GET',
                '/qux',
                'qux-handler',
                'qux-name',
                ['qux-middleware']
            )->map(
                'PUT',
                '/quux',
                'quux-handler',
                'quux-name',
                ['quux-middleware']
            );

        $this->group->getByName('bar-name')
            ->map(
                'GET',
                '/quuz',
                'quuz-handler',
                'quuz-name',
                ['quuz-middleware']
            )->map(
                'PUT',
                '/corge',
                'corge-handler',
                'corge-name',
                ['corge-middleware']
            );

        $routes = $this->group->getIterator();
        $this->assertCount(5, $routes);

        foreach ($this->group->getIterator() as $route) {
            if ($route->getName() == 'quuz-name') {
                $this->assertEquals(
                    ['foo-middleware', 'bar-middleware', 'quuz-middleware'],
                    $route->getMiddlewareStack()
                );
            }

            if ($route->getName() == 'corge-name') {
                $this->assertEquals(
                    ['foo-middleware', 'bar-middleware', 'corge-middleware'],
                    $route->getMiddlewareStack()
                );
            }
        }
    }
}
