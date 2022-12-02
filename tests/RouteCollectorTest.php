<?php

namespace PhpStandard\Router\Tests;

use PhpStandard\Router\Route;
use PhpStandard\Router\RouteCollector;
use PhpStandard\Router\RouteGroup;
use PHPUnit\Framework\TestCase;

class RouteCollectorTest extends TestCase
{
    private RouteCollector $rc;

    protected function setUp(): void
    {
        $this->rc = new RouteCollector([
            'foo-middleware'
        ]);
    }

    /** @test */
    public function canGetRoutesWithNestedGroupsAndPrependGroupMiddlewares()
    {
        $this->rc
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

        $this->rc
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

        $this->rc->getByName('bar-name')
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

        $routes = $this->rc->getRoutes();
        $this->assertCount(5, $routes);

        foreach ($routes as $route) {
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
