<?php

declare(strict_types=1);

namespace PhpStandard\Router\Exceptions;

use PhpStandard\Http\Server\Exceptions\DispatcherExceptionInterface;

/** @package PhpStandard\Router\Exceptions */
class Exception extends \Exception implements DispatcherExceptionInterface
{
}
