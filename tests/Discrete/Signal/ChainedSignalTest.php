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

    public function testShiftedChaining()
    {
        $sig1 = new FunctionSignal(function (int $instant) {
            return $instant*2;
        });
        $sig2 = new FunctionSignal(function (int $instant) {
            return $instant*$instant;
        });

        $signal = new ChainedSignal($sig1, $sig2, 5, -3, 4);

        $this->assertEquals(-6, $signal->at(new Context(0)));
        $this->assertEquals(-4, $signal->at(new Context(1)));
        $this->assertEquals(-2, $signal->at(new Context(2)));
        $this->assertEquals(0, $signal->at(new Context(3)));
        $this->assertEquals(2, $signal->at(new Context(4)));

        $this->assertEquals(81, $signal->at(new Context(5)));
        $this->assertEquals(100, $signal->at(new Context(6)));
        $this->assertEquals(121, $signal->at(new Context(7)));
        $this->assertEquals(144, $signal->at(new Context(8)));
        $this->assertEquals(169, $signal->at(new Context(9)));
    }
}
