<?php

declare(strict_types=1);

namespace PhpStandard\Router;

use IteratorAggregate;
use Traversable;

/**
 * @package PhpStandard\Router
 * @extends IteratorAggregate<Map>
 */
interface MapperInterface extends IteratorAggregate
{
    /** @return Traversable<Map>  */
    public function getIterator(): Traversable;
}
