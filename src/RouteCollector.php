<?php

declare(strict_types=1);

namespace PhpStandard\Router;

use Traversable;

/** @package PhpStandard\Router */
class RouteCollector extends RouteGroup
{
    /**
     * @param null|array $middlewares
     * @return void
     */
    public function __construct(
        ?array $middlewares = null
    ) {
        $this->setMiddlewares(...$middlewares ?? []);
    }

    /** @return Traversable<Route>  */
    public function getIterator(): Traversable
    {
        /** @var array<Route> $map */
        $map = [];

        foreach ($this->collection as $entity) {
            $entity = $entity->withPrependedMiddleware(...$this->middlewares);

            if ($entity instanceof Route) {
                yield $entity;
                continue;
            }

            if ($entity instanceof RouteGroup) {
                yield from $entity->getIterator();
                continue;
            }
        }

        return $map;
    }
}
