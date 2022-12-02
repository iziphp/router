<?php

declare(strict_types=1);

namespace PhpStandard\Router\Exceptions;

/** @package PhpStandard\Router\Exceptions */
class RequestHandlerNotResolvedException extends Exception
{
    /**
     * @param string $handler
     * @return void
     */
    public function __construct(
        private string $handler
    ) {
        parent::__construct("Request handler '$handler' not resolved");
    }

    /** @return string  */
    public function getHandler(): string
    {
        return $this->handler;
    }
}
