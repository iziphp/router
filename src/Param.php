<?php

declare(strict_types=1);

namespace PhpStandard\Router;

use PhpStandard\Http\Server\RouteParamInterface;

/** @package PhpStandard\Router */
class Param implements RouteParamInterface
{
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __construct(
        private string $key,
        private mixed $value
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function getValue(): mixed
    {
        return $this->value;
    }
}
