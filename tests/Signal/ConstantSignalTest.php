<?php


namespace Litipk\MacPhply\Tests\Model;

use PHPUnit\Framework\TestCase;


class ConstantSignalTest extends TestCase
{
    public function testConstructor()
    {
        $model = new \Litipk\MacPhply\ConstantSignal(42);
    }

    public function testAt()
    {
        $model = new \Litipk\MacPhply\ConstantSignal(42);

        $this->assertEquals(42, $model->at(new \Litipk\MacPhply\Context(0)));
        $this->assertEquals(42, $model->at(new \Litipk\MacPhply\Context(1)));
        $this->assertEquals(42, $model->at(new \Litipk\MacPhply\Context(2)));
    }
}
