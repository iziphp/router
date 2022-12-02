<?php

declare(strict_types=1);

namespace PhpStandard\Router;

use PhpStandard\Router\Traits\MiddlewareAwareTrait;

class RouteGroup
{
    use MiddlewareAwareTrait;

    /** @var (Route|RouteGroup)[] $collection */
    protected array $collection = [];

    public function __construct(
        private ?string $prefix = null,
        private ?string $name = null,
        ?array $middlewares = null
    ) {
        $this->prefix = $prefix;
        $this->name = $name;
        $this->setMiddlewares(...$middlewares ?? []);
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function withPrefix(?string $prefix): RouteGroup
    {
        $that = clone $this;
        $that->prefix = $prefix;
        return $that;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function add(Route|RouteGroup $route): static
    {
        $this->collection[] = $route;
        return $this;
    }

    public function map(
        string $method,
        string $path,
        callable|string $handle,
        ?string $name = null,
        ?array $middlewares = null
    ): static {
        $route = new Route($method, $path, $handle, $name, $middlewares);
        $this->add($route);

        return $this;
    }

    public function getRoutes(): array
    {
        /** @var array<Route> $map */
        $map = [];

        foreach ($this->collection as $entity) {
            $entity = $entity->withPrependedMiddleware(...$this->middlewares);

            if ($entity instanceof Route) {
                $map[] = $this->prefix
                    ? $entity->withPath($this->prefix . $entity->getPath())
                    : $entity;

                continue;
            }

            if ($entity instanceof RouteGroup) {
                $entity = $this->prefix
                    ? $entity->withPrefix($this->prefix . $entity->getPrefix())
                    : $entity;

                $map = array_merge($map, $entity->getRoutes());

                continue;
            }
        }

        return $map;
    }

    public function getByName(string $name): Route|RouteGroup|null
    {
        foreach ($this->collection as $item) {
            if ($item->getName() == $name) {
                return $item;
            }

            if ($item instanceof RouteGroup) {
                $result = $item->getByName($name);

                if ($result) {
                    return $result;
                }
            }
        }

        return null;
    }
}
