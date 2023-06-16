<?php

namespace PhpStandard\Router\Exceptions;

use Throwable;

class MiddlewareNotResolvedException extends Exception
{
    /**
     * @param string $middleware
     * @return void
     */
    public function __construct(
        private string $middleware,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            "Middleware '$middleware' not resolved",
            $code,
            $previous
        );
    }

    /** @return string  */
    public function getMiddleware(): string
    {
        return $this->middleware;
    }
}
