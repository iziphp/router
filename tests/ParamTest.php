<?php

declare(strict_types=1);

namespace PhpStandard\Router\Tests;

use PhpStandard\Router\Param;
use PHPUnit\Framework\TestCase;

class ParamTest extends TestCase
{
    /** @test */
    public function canGetParamName()
    {
        $param = new Param('foo', 'bar');
        $this->assertEquals('foo', $param->getKey());
    }

    /** @test */
    public function canGetParamValue()
    {
        $param = new Param('foo', 'bar');
        $this->assertEquals('bar', $param->getValue());
    }
}
