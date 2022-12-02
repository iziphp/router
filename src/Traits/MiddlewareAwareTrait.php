<?php

declare(strict_types=1);

namespace PhpStandard\Router\Traits;

use Psr\Http\Server\MiddlewareInterface;

trait MiddlewareAwareTrait
{
    /** @var array<MiddlewareInterface|string> */
    protected array $middlewares = [];

    /**
     * @param MiddlewareInterface|string ...$middlewares
     * @return static
     */
    public function withAppendedMiddleware(
        MiddlewareInterface|string ...$middlewares
    ): static {
        $that = clone $this;
        $that->middlewares = array_merge($that->middlewares, $middlewares);

        return $that;
    }

    /**
     * @param MiddlewareInterface|string ...$middlewares
     * @return static
     */
    public function withPrependedMiddleware(
        MiddlewareInterface|string ...$middlewares
    ): static {
        $that = clone $this;
        $that->middlewares = array_merge($middlewares, $that->middlewares);

        return $that;
    }

    /** @return array<MiddlewareInterface|string>  */
    public function getMiddlewareStack(): array
    {
        return $this->middlewares;
    }

    /**
     * @param MiddlewareInterface|string ...$middlewares
     * @return static
     */
    protected function setMiddlewares(
        MiddlewareInterface|string ...$middlewares
    ): static {
        $this->middlewares = $middlewares;
        return $this;
    }
}
