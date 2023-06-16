<?php

declare(strict_types=1);

namespace PhpStandard\Router\Exceptions;

use PhpStandard\Http\Server\Exceptions\MethodNotAllowedExceptionInterface;

class MethodNotAllowedException extends Exception implements
    MethodNotAllowedExceptionInterface
{
}
