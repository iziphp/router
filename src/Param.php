<?php

declare(strict_types=1);

namespace PhpStandard\Router;

/** @package PhpStandard\Router */
class Param
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

    /** @return string  */
    public function getKey(): string
    {
        return $this->key;
    }

    /** @return mixed  */
    public function getValue(): mixed
    {
        return $this->value;
    }
}
