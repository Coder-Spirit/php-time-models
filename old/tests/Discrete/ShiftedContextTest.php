<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Tests\Discrete;


use Litipk\TimeModels\Discrete\Context\ShiftedContext;
use Litipk\TimeModels\Discrete\Context\SimpleContext;
use Litipk\TimeModels\Discrete\Signals\ConstantSignal;

use PHPUnit\Framework\TestCase;


class ShiftedContextTest extends TestCase
{
    public function testGetSignal()
    {
        $signal = new ConstantSignal(0);
        $srcCtx = new SimpleContext(0, [], null, $signal);
        $ctx = new ShiftedContext($srcCtx, 0);

        $this->assertEquals($signal, $ctx->getSignal());
    }

    public function testWithSignal()
    {
        $signal = new ConstantSignal(0);
        $srcCtx = new SimpleContext(0);
        $ctx = (new ShiftedContext($srcCtx, 0))->withSignal($signal);

        $this->assertEquals($signal, $ctx->getSignal());
    }

    public function testWithInstant()
    {
        $srcCtx = new SimpleContext(42);
        $ctx = (new ShiftedContext($srcCtx, 3))->withInstant(39);

        $this->assertEquals(42, $ctx->getInstant());
    }
}
