<?php


namespace Litipk\MacPhply\Tests\Model;

use PHPUnit\Framework\TestCase;


class ConstantSignalTest extends TestCase
{
    public function testConstructor()
    {
        $signal = new \Litipk\MacPhply\ConstantSignal(42);
    }

    public function testAt()
    {
        $signal = new \Litipk\MacPhply\ConstantSignal(42);

        $this->assertEquals(42, $signal->at(new \Litipk\MacPhply\Context(0, $signal)));
        $this->assertEquals(42, $signal->at(new \Litipk\MacPhply\Context(1, $signal)));
        $this->assertEquals(42, $signal->at(new \Litipk\MacPhply\Context(2, $signal)));
    }
}
