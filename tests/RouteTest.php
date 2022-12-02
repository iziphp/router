<?php

namespace PhpStandard\Router\Tests;

use PhpStandard\Router\Param;
use PhpStandard\Router\Route;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouteTest extends TestCase
{
    private Route $route;

    protected function setUp(): void
    {
        $middleware = new class implements MiddlewareInterface
        {
            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ): ResponseInterface {
                return $handler->handle($request);
            }
        };

        $this->route = new Route(
            'PATCH',
            '/foo',
            'foo-handler',
            'foo-name',
            ['foo-middleware']
        );
    }

    /** @test */
    public function canGetMethod()
    {
        $this->assertEquals('PATCH', $this->route->getMethod());
    }

    /** @test */
    public function canGetPath()
    {
        $this->assertEquals('/foo[/]?', $this->route->getPath());
    }

    /** @test */
    public function canGetHandler()
    {
        $this->assertEquals('foo-handler', $this->route->getHandler());
    }

    /** @test */
    public function canGetName()
    {
        $this->assertEquals('foo-name', $this->route->getName());
    }

    /** @test */
    public function canCloneWithPath()
    {
        $route = $this->route->withPath('/bar');
        $this->assertEquals('/foo[/]?', $this->route->getPath());
        $this->assertEquals('/bar[/]?', $route->getPath());
    }

    /** @test */
    public function canGetParams()
    {
        $this->assertEquals([], (array) $this->route->getParams());
    }

    /** @test */
    public function canCloneWithParam()
    {
        $route = $this->route->withParam(
            new Param('foo', 'bar'),
            new Param('baz', 'qux')
        );

        $this->assertEquals([], (array)$this->route->getParams());
        $this->assertEquals(
            [
                'foo' => 'bar',
                'baz' => 'qux',
            ],
            (array)$route->getParams()
        );
    }

    /** @test */
    public function canGetMiddlewareStack()
    {
        $this->assertEquals(
            ['foo-middleware'],
            $this->route->getMiddlewareStack()
        );
    }

    /** @test */
    public function canCloneWithAppendedMiddleware()
    {
        $route = $this->route
            ->withAppendedMiddleware('bar-middleware')
            ->withAppendedMiddleware('baz-middleware');
        $this->assertEquals(
            ['foo-middleware'],
            (array)$this->route->getMiddlewareStack()
        );
        $this->assertEquals(
            ['foo-middleware', 'bar-middleware', 'baz-middleware'],
            (array)$route->getMiddlewareStack()
        );
    }

    /**  @test */
    public function canCloneWithPrependedMiddleware()
    {
        $route = $this->route
            ->withPrependedMiddleware('bar-middleware', 'qux-middleware')
            ->withPrependedMiddleware('baz-middleware');
        $this->assertEquals(
            ['foo-middleware'],
            (array)$this->route->getMiddlewareStack()
        );
        $this->assertEquals(
            [
                'baz-middleware',
                'bar-middleware',
                'qux-middleware',
                'foo-middleware'
            ],
            (array)$route->getMiddlewareStack()
        );
    }
}
