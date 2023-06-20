<?php

namespace PhpStandard\Router\Attributes;

use Attribute;

/** @package PhpStandard\Router\Attributes */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Path
{
    /**
     * @param string $prefix
     * @return void
     */
    public function __construct(
        public readonly string $prefix
    ) {
    }
}
