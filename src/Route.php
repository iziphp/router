<?php

declare(strict_types=1);

namespace PhpStandard\Router;

use ArrayIterator;
use PhpStandard\Router\Traits\MiddlewareAwareTrait;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Route
{
    use MiddlewareAwareTrait;

    /** @var array $parameters Route parameters */
    private array $parameters = [];

    public function __construct(
        private string $method,
        private string $path,
        private RequestHandlerInterface|string $handler,
        private ?string $name = null,
        ?array $middlewares = null
    ) {
        $this->path = $this->sanitizePath($path);
        $this->handler = $handler;

        $this->setMiddlewares(...$middlewares ?? []);
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function withPath(string $path): Route
    {
        $that = clone $this;
        $that->path = $this->sanitizePath($path);
        return $that;
    }

    public function getHandler(): RequestHandlerInterface|string
    {
        return $this->handler;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getParams(): ArrayIterator
    {
        return new ArrayIterator($this->parameters);
    }

    public function withParam(Param ...$params): Route
    {
        $that = clone $this;

        foreach ($params as $param) {
            $that->parameters[$param->getKey()] = $param->getValue();
        }

        return $that;
    }

    public function resolve(ContainerInterface $container): self
    {
        $this->resolveHandler($container);
        $this->resolveMiddlewares($container);
        return $this;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator(
            array_merge($this->middlewares, [$this->handler])
        );
    }

    private function sanitizePath(string $path): string
    {
        if ($path != '*' && substr($path, -4) !== '[/]?') {
            $path = rtrim($path, '/') . '[/]?';
        }

        return $path;
    }

    private function resolveHandler(
        ContainerInterface $container
    ): void {
        if (is_string($this->handler)) {
            $handler = $container->get($this->handler);

            if (!($handler instanceof RequestHandlerInterface)) {
                // Throw exception
            }

            $this->handler = $handler;
        }
    }

    private function resolveMiddlewares(ContainerInterface $container): void
    {
        /** @var array<MiddlewareInterface> $resolved */
        $resolved = [];

        foreach ($this->middlewares as $middleware) {
            if (is_string($middleware)) {
                $middleware = $container->get($middleware);
            }

            if (!($middleware instanceof MiddlewareInterface)) {
                // Throw exception
            }

            $resolved[] = $middleware;
        }

        $this->middlewares = $resolved;
    }
}
