<?php


namespace Litipk\TimeModels\Tests\Discrete\Signal;


use Litipk\TimeModels\Discrete\ChainedSignal;
use Litipk\TimeModels\Discrete\ConstantSignal;
use Litipk\TimeModels\Discrete\Context;
use Litipk\TimeModels\Discrete\SimpleContext;
use Litipk\TimeModels\Discrete\FunctionSignal;

use PHPUnit\Framework\TestCase;


class ChainedSignalTest extends TestCase
{
    public function testSimpleChaining()
    {
        $sig1 = new ConstantSignal(42);
        $sig2 = new ConstantSignal(100);

        $signal = new ChainedSignal($sig1, $sig2, 5);

        $this->assertEquals(42, $signal->at(new SimpleContext(0)));
        $this->assertEquals(42, $signal->at(new SimpleContext(1)));
        $this->assertEquals(42, $signal->at(new SimpleContext(2)));
        $this->assertEquals(42, $signal->at(new SimpleContext(3)));
        $this->assertEquals(42, $signal->at(new SimpleContext(4)));

        $this->assertEquals(100, $signal->at(new SimpleContext(5)));
        $this->assertEquals(100, $signal->at(new SimpleContext(6)));
        $this->assertEquals(100, $signal->at(new SimpleContext(7)));
        $this->assertEquals(100, $signal->at(new SimpleContext(8)));
        $this->assertEquals(100, $signal->at(new SimpleContext(9)));
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

        $this->assertEquals(-6, $signal->at(new SimpleContext(0)));
        $this->assertEquals(-4, $signal->at(new SimpleContext(1)));
        $this->assertEquals(-2, $signal->at(new SimpleContext(2)));
        $this->assertEquals(0, $signal->at(new SimpleContext(3)));
        $this->assertEquals(2, $signal->at(new SimpleContext(4)));

        $this->assertEquals(81, $signal->at(new SimpleContext(5)));
        $this->assertEquals(100, $signal->at(new SimpleContext(6)));
        $this->assertEquals(121, $signal->at(new SimpleContext(7)));
        $this->assertEquals(144, $signal->at(new SimpleContext(8)));
        $this->assertEquals(169, $signal->at(new SimpleContext(9)));
    }

    public function testShiftedChaining_withMemory()
    {
        $sig1 = new FunctionSignal(function (int $instant) {
            return $instant*2;
        });
        $sig2 = new FunctionSignal(function (int $instant, Context $ctx) {
            return $ctx->prevSignal(1)+1;
        });

        $signal = new ChainedSignal($sig1, $sig2, 5, -3, 4);

        $this->assertEquals(-6, $signal->at(new SimpleContext(0)));
        $this->assertEquals(-4, $signal->at(new SimpleContext(1)));
        $this->assertEquals(-2, $signal->at(new SimpleContext(2)));
        $this->assertEquals(0, $signal->at(new SimpleContext(3)));
        $this->assertEquals(2, $signal->at(new SimpleContext(4)));

        $this->assertEquals(3, $signal->at(new SimpleContext(5)));
        $this->assertEquals(4, $signal->at(new SimpleContext(6)));
        $this->assertEquals(5, $signal->at(new SimpleContext(7)));
        $this->assertEquals(6, $signal->at(new SimpleContext(8)));
        $this->assertEquals(7, $signal->at(new SimpleContext(9)));
    }

    public function testShiftedChaining_withMemoryAndInstant()
    {
        $sig1 = new FunctionSignal(function (int $instant) {
            return $instant*2;
        });
        $sig2 = new FunctionSignal(function (int $instant, Context $ctx) {
            return $ctx->prevSignal(1)+$instant;
        });

        $signal = new ChainedSignal($sig1, $sig2, 5, -3, 4);

        $this->assertEquals(-6, $signal->at(new SimpleContext(0)));
        $this->assertEquals(-4, $signal->at(new SimpleContext(1)));
        $this->assertEquals(-2, $signal->at(new SimpleContext(2)));
        $this->assertEquals(0, $signal->at(new SimpleContext(3)));
        $this->assertEquals(2, $signal->at(new SimpleContext(4)));

        $this->assertEquals(11, $signal->at(new SimpleContext(5)));
        $this->assertEquals(21, $signal->at(new SimpleContext(6)));
        $this->assertEquals(32, $signal->at(new SimpleContext(7)));
        $this->assertEquals(44, $signal->at(new SimpleContext(8)));
        $this->assertEquals(57, $signal->at(new SimpleContext(9)));
    }

    public function testSimpleTripleChaining()
    {
        $sig1 = new ConstantSignal(42);
        $sig2 = new ConstantSignal(100);
        $sig3 = new ConstantSignal(150);

        $sig4 = new ChainedSignal($sig1, $sig2, 5);
        $sig5 = new ChainedSignal($sig4, $sig3, 15);

        $this->assertEquals(42, $sig5->at(new SimpleContext(0)));
        $this->assertEquals(42, $sig5->at(new SimpleContext(4)));
        $this->assertEquals(100, $sig5->at(new SimpleContext(5)));
        $this->assertEquals(100, $sig5->at(new SimpleContext(14)));
        $this->assertEquals(150, $sig5->at(new SimpleContext(15)));
        $this->assertEquals(150, $sig5->at(new SimpleContext(16)));
    }

    public function testShiftedTripleChaining()
    {
        $sig1 = new FunctionSignal(function (int $instant) {
            return $instant*2;
        });
        $sig2 = new FunctionSignal(function (int $instant, Context $ctx) {
            return $ctx->prevSignal(1)+$instant;
        });
        $sig3 = new ConstantSignal(150);

        $sig4 = new ChainedSignal($sig1, $sig2, 5, -3, 4);
        $sig5 = new ChainedSignal($sig4, $sig3, 15, -1);

        $this->assertEquals(-6, $sig5->at(new SimpleContext(1)));
        $this->assertEquals(-4, $sig5->at(new SimpleContext(2)));
        $this->assertEquals(-2, $sig5->at(new SimpleContext(3)));
        $this->assertEquals(0, $sig5->at(new SimpleContext(4)));

        $this->assertEquals(2, $sig5->at(new SimpleContext(5)));
        $this->assertEquals(11, $sig5->at(new SimpleContext(6)));
        $this->assertEquals(21, $sig5->at(new SimpleContext(7)));
        $this->assertEquals(32, $sig5->at(new SimpleContext(8)));
        $this->assertEquals(44, $sig5->at(new SimpleContext(9)));

        $this->assertEquals(150, $sig5->at(new SimpleContext(15)));
        $this->assertEquals(150, $sig5->at(new SimpleContext(16)));
    }
}
