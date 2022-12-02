<?php

declare(strict_types=1);

namespace PhpStandard\Router\Exceptions;

class RequestHandlerNotResolvedException extends Exception
{
    public function __construct(
        private string $handler
    ) {
        parent::__construct("Request handler '$handler' not resolved");
    }

    public function getHandler(): string
    {
        return $this->handler;
    }
}
