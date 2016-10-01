<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Tests\Discrete\Signal;


use Litipk\TimeModels\Discrete\Context\Context;
use Litipk\TimeModels\Discrete\Context\SimpleContext;
use Litipk\TimeModels\Discrete\Signals\ChainedSignal;
use Litipk\TimeModels\Discrete\Signals\ConstantSignal;
use Litipk\TimeModels\Discrete\Signals\FunctionSignal;

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

    public function testGetComponentSignals()
    {
        $sig1 = new ConstantSignal(42);
        $sig2 = new ConstantSignal(100);

        $signal = new ChainedSignal($sig1, $sig2, 5);

        $this->assertEquals(2, count($signal->getComponentSignals()));

        $recoveredSig1 = $signal->getComponentSignals()[0];
        $this->assertTrue($recoveredSig1 instanceof ConstantSignal);

        $recoveredSig2 = $signal->getComponentSignals()[1];
        $this->assertTrue($recoveredSig2 instanceof ConstantSignal);
    }
}
