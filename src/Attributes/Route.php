<?php

namespace Easy\Router\Attributes;

use Attribute;
use Easy\Http\Message\RequestMethod;

/** @package Easy\Router\Attributes */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Route
{
    public const PRIORITY_LOW = 0;
    public const PRIORITY_NORMAL = 10;
    public const PRIORITY_HIGH = 100;

    /**
     * @param string $path
     * @param RequestMethod $method
     * @param int $priority
     * @return void
     */
    public function __construct(
        public readonly string $path,
        public readonly RequestMethod $method = RequestMethod::GET,
        public readonly int $priority = self::PRIORITY_NORMAL
    ) {
    }
}
