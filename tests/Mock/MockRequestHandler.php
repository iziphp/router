<?php

declare(strict_types=1);

namespace PhpStandard\Router\Tests\Mock;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MockRequestHandler implements RequestHandlerInterface
{
    public function handle(
        ServerRequestInterface $request
    ): ResponseInterface {
        $resp =  new Response();
        $resp->getBody()->write(
            'There are three kinds of lies: Lies, damned lies, and benchmarks.'
        );
        return $resp;
    }
}
