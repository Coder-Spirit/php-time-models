<?php


namespace Litipk\MacPhply\Tests\Model;

use PHPUnit\Framework\TestCase;


class FunctionSignalTest extends TestCase
{
    public function testConstructor()
    {
        $model = new \Litipk\MacPhply\FunctionSignal(function () {});
    }

    public function testAt_SimpleTimeFunction()
    {
        $model = new \Litipk\MacPhply\FunctionSignal(function (int $instant) {
            return $instant*2;
        });

        $this->assertEquals(0, $model->at(new \Litipk\MacPhply\Context(0)));
        $this->assertEquals(2, $model->at(new \Litipk\MacPhply\Context(1)));
        $this->assertEquals(4, $model->at(new \Litipk\MacPhply\Context(2)));
    }
}
