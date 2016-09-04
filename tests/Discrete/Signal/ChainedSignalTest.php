<?php


namespace Litipk\TimeModels\Tests\Discrete\Signal;


use Litipk\TimeModels\Discrete\ChainedSignal;
use Litipk\TimeModels\Discrete\ConstantSignal;
use Litipk\TimeModels\Discrete\Context;
use Litipk\TimeModels\Discrete\FunctionSignal;

use PHPUnit\Framework\TestCase;


class ChainedSignalTest extends TestCase
{
    public function testSimpleChaining()
    {
        $sig1 = new ConstantSignal(42);
        $sig2 = new ConstantSignal(100);

        $signal = new ChainedSignal($sig1, $sig2, 5);

        $this->assertEquals(42, $signal->at(new Context(0)));
        $this->assertEquals(42, $signal->at(new Context(1)));
        $this->assertEquals(42, $signal->at(new Context(2)));
        $this->assertEquals(42, $signal->at(new Context(3)));
        $this->assertEquals(42, $signal->at(new Context(4)));

        $this->assertEquals(100, $signal->at(new Context(5)));
        $this->assertEquals(100, $signal->at(new Context(6)));
        $this->assertEquals(100, $signal->at(new Context(7)));
        $this->assertEquals(100, $signal->at(new Context(8)));
        $this->assertEquals(100, $signal->at(new Context(9)));
    }
}
