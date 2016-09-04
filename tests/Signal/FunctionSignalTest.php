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

        $this->assertEquals(0, $signal->at(new \Litipk\MacPhply\Context(0)));
        $this->assertEquals(2, $signal->at(new \Litipk\MacPhply\Context(1)));
        $this->assertEquals(4, $signal->at(new \Litipk\MacPhply\Context(2)));
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

    public function testAt_CrossReferredFunction()
    {
        $sig1 = new \Litipk\MacPhply\FunctionSignal(function (int $instant, Context $ctx) {
            return ($instant <= 0)
                ? 2
                : 3 * $ctx->prevEnvSignals('sig2', 1);
        });

        $sig2 = new \Litipk\MacPhply\FunctionSignal(function (int $instant, Context $ctx) {
            return ($instant <= 0)
                ? 5
                : 7 * $ctx->prevEnvSignals('sig1', 1);
        });

        $model = new \Litipk\MacPhply\Model();
        $model = $model
                    ->withSignal('sig1', $sig1)
                    ->withSignal('sig2', $sig2);

        $this->assertEquals(2, $sig1->at(new \Litipk\MacPhply\Context(0, $sig1, $model)));
        $this->assertEquals(5, $sig2->at(new \Litipk\MacPhply\Context(0, $sig2, $model)));

        $this->assertEquals(15, $sig1->at(new \Litipk\MacPhply\Context(1, $sig1, $model)));
        $this->assertEquals(14, $sig2->at(new \Litipk\MacPhply\Context(1, $sig2, $model)));
    }
}
