<?php

namespace PhpStandard\Router\Exceptions;

use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class RouteNotFoundException extends Exception
{
    public function __construct(
        private ServerRequestInterface $request,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $uri = $request->getUri();
        $method = $request->getMethod();

        $message = sprintf(
            'Route not found for method "[%s]<%s>"',
            $method,
            (string) $uri
        );

        parent::__construct(
            $message,
            $code,
            $previous
        );
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
