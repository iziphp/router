<?php

declare(strict_types=1);

namespace PhpStandard\Router;

use IteratorAggregate;
use PhpStandard\Router\Traits\MiddlewareAwareTrait;
use Traversable;

/** @package PhpStandard\Router */
class RouteGroup implements IteratorAggregate
{
    use MiddlewareAwareTrait;

    /** @var (Route|RouteGroup)[] $collection */
    protected array $collection = [];

    /**
     * @param null|string|array<string> $prefix
     * @param null|string $name
     * @param null|array $middlewares
     * @return void
     */
    public function __construct(
        private string|array|null $prefix = null,
        private ?string $name = null,
        ?array $middlewares = null
    ) {
        $prefix = $prefix ?: '';
        $parts = is_string($prefix) ? [$prefix] : $prefix;
        $this->prefix = $this->sanitizePrefix(...$parts);

        $this->name = $name;
        $this->setMiddlewares(...$middlewares ?? []);
    }

    /** @return null|string  */
    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    /**
     * @param string ...$parts
     * @return RouteGroup
     */
    public function withPrefix(string ...$parts): RouteGroup
    {
        $that = clone $this;
        $that->prefix = $that->sanitizePrefix(...$parts);
        return $that;
    }

    /** @return null|string  */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param Route|RouteGroup $route
     * @return static
     */
    public function add(Route|RouteGroup $route): static
    {
        $this->collection[] = $route;
        return $this;
    }

    /**
     * @param string $method
     * @param string $path
     * @param callable|string $handle
     * @param null|string $name
     * @param null|array $middlewares
     * @return static
     */
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

    /** @return Traversable<Route>  */
    public function getIterator(): Traversable
    {
        foreach ($this->collection as $entity) {
            $entity = $entity->withPrependedMiddleware(...$this->middlewares);

            if ($entity instanceof Route) {
                yield $this->prefix
                    ? $entity->withPath($this->prefix, $entity->getPath())
                    : $entity;

                continue;
            }

            if ($entity instanceof RouteGroup) {
                $entity = $this->prefix
                    ? $entity->withPrefix($this->prefix, $entity->getPrefix())
                    : $entity;

                yield from $entity->getIterator();

                continue;
            }
        }
    }

    /**
     * @param string $name
     * @return Route|RouteGroup|null
     */
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

    /**
     * @param string ...$parts
     * @return string
     */
    private function sanitizePrefix(string ...$parts): string
    {
        $prefix = implode('/', $parts);
        $prefix = preg_replace('/\/+/', '/', $prefix);
        $prefix = trim($prefix, '/');
        $prefix = '/' . $prefix;

        return $prefix;
    }
}
