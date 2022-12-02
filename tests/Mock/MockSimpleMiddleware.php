<?php

namespace PhpStandard\Router\Tests\Mock;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MockSimpleMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ?string $key = null,
        private ?string $value = null
    ) {
    }


    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $resp = $handler->handle($request);

        if ($this->key && $this->value) {
            $resp = $resp->withHeader($this->key, $this->value);
        }

        return $resp->withAddedHeader(
            'X-SIMPLE-MIDDLEWARE',
            'This header added in ' . __CLASS__
        );
    }
}
