<?php

declare(strict_types=1);

namespace PhpStandard\Router;

class Param
{
    public function __construct(
        private string $key,
        private mixed $value
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
