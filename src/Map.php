<?php

declare(strict_types=1);

namespace PhpStandard\Router;

use PhpStandard\Http\Message\RequestMethodEnum;
use PhpStandard\Router\Mapper\Mapper;
use Psr\Http\Server\RequestHandlerInterface;

/** @package PhpStandard\Router */
class Map
{
    public RequestMethodEnum $method = RequestMethodEnum::GET;
    public null|Group|Mapper $parent = null;
    public ?string $path = null;
    public RequestHandlerInterface|string $handler;
    public ?string $name = null;
    public MiddlewareCollection $middlewares;

    /** @return void  */
    public function __construct()
    {
        $this->middlewares = new MiddlewareCollection($this);
    }

    /** @return string  */
    public function getPath(): string
    {
        if ($this->parent instanceof Group) {
            return $this->parent->getPrefix() . $this->getSanitizePath();
        }

        return $this->getSanitizePath();
    }

    /** @return string */
    private function getSanitizePath(): string
    {
        $path = preg_replace('/\/+/', '/', $this->path ?: '');
        $path = trim($path ?: '', '/');
        $path = '/' . $path;

        return $path;
    }
}
