<?php


namespace Litipk\TimeModels\Tests\Discrete\Signal;


use Litipk\TimeModels\Discrete\ConstantSignal;
use Litipk\TimeModels\Discrete\SimpleContext;

use PHPUnit\Framework\TestCase;


class ConstantSignalTest extends TestCase
{
    public function testConstructor()
    {
        $signal = new ConstantSignal(42);
    }

    public function testAt()
    {
        $signal = new ConstantSignal(42);

        $this->assertEquals(42, $signal->at(new SimpleContext(0)));
        $this->assertEquals(42, $signal->at(new SimpleContext(1)));
        $this->assertEquals(42, $signal->at(new SimpleContext(2)));
    }
}
