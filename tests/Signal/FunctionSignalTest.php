<?php


namespace Litipk\MacPhply\Tests\Model;

use Litipk\MacPhply\Context;
use PHPUnit\Framework\TestCase;


class FunctionSignalTest extends TestCase
{
    public function testConstructor()
    {
        $signal = new \Litipk\MacPhply\FunctionSignal(function () {});
    }

    public function testAt_SimpleTimeFunction()
    {
        $signal = new \Litipk\MacPhply\FunctionSignal(function (int $instant) {
            return $instant*2;
        });

        $this->assertEquals(0, $signal->at(new \Litipk\MacPhply\Context(0, $signal)));
        $this->assertEquals(2, $signal->at(new \Litipk\MacPhply\Context(1, $signal)));
        $this->assertEquals(4, $signal->at(new \Litipk\MacPhply\Context(2, $signal)));
    }

    public function testAt_AutoReferredFunction()
    {
        $signal = new \Litipk\MacPhply\FunctionSignal(function (int $instant, Context $ctx) {
            return ($instant <= 0)
                ? 1
                : 2 * $ctx->prevSignal(1);
        });

        $this->assertEquals(1, $signal->at(new \Litipk\MacPhply\Context(0, $signal)));
        $this->assertEquals(2, $signal->at(new \Litipk\MacPhply\Context(1, $signal)));
        $this->assertEquals(4, $signal->at(new \Litipk\MacPhply\Context(2, $signal)));
        $this->assertEquals(8, $signal->at(new \Litipk\MacPhply\Context(3, $signal)));
    }
}
