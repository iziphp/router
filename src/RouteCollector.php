<?php

declare(strict_types=1);

namespace PhpStandard\Router;

class RouteCollector extends RouteGroup
{
    public function __construct(
        ?array $middlewares = null
    ) {
        $this->setMiddlewares(...$middlewares ?? []);
    }

    /** @return array<Route>  */
    public function getRoutes(): array
    {
        /** @var array<Route> $map */
        $map = [];

        foreach ($this->collection as $entity) {
            $entity = $entity->withPrependedMiddleware(...$this->middlewares);

            if ($entity instanceof Route) {
                $map[] = $entity;
                continue;
            }

            if ($entity instanceof RouteGroup) {
                $map = array_merge($map, $entity->getRoutes());
                continue;
            }
        }

        return $map;
    }
}
