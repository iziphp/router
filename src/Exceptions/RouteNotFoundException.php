<?php

declare(strict_types=1);

namespace PhpStandard\Router\Exceptions;

use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/** @package PhpStandard\Router\Exceptions */
class RouteNotFoundException extends Exception
{
    /**
     * @param ServerRequestInterface $request
     * @param int $code
     * @param null|Throwable $previous
     * @return void
     */
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

    /** @return ServerRequestInterface  */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
