<?php


namespace Litipk\TimeModels\Tests\Discrete\Signal;


use Litipk\TimeModels\Discrete\Context;
use Litipk\TimeModels\Discrete\FunctionSignal;
use Litipk\TimeModels\Discrete\Model;

use PHPUnit\Framework\TestCase;


class FunctionSignalTest extends TestCase
{
    public function testConstructor()
    {
        $signal = new FunctionSignal(function () {});
    }

    public function testAt_SimpleTimeFunction()
    {
        $signal = new FunctionSignal(function (int $instant) {
            return $instant*2;
        });

        $this->assertEquals(0, $signal->at(new Context(0)));
        $this->assertEquals(2, $signal->at(new Context(1)));
        $this->assertEquals(4, $signal->at(new Context(2)));
    }

    public function testAt_AutoReferredFunction()
    {
        $signal = new FunctionSignal(function (int $instant, Context $ctx) {
            return ($instant <= 0)
                ? 1
                : 2 * $ctx->prevSignal(1);
        });

        $this->assertEquals(1, $signal->at(new Context(0, $signal)));
        $this->assertEquals(2, $signal->at(new Context(1, $signal)));
        $this->assertEquals(4, $signal->at(new Context(2, $signal)));
        $this->assertEquals(8, $signal->at(new Context(3, $signal)));
    }

    public function testAt_CrossReferredFunction()
    {
        $sig1 = new FunctionSignal(function (int $instant, Context $ctx) {
            return ($instant <= 0)
                ? 2
                : 3 * $ctx->prevEnvSignals('sig2', 1);
        });

        $sig2 = new FunctionSignal(function (int $instant, Context $ctx) {
            return ($instant <= 0)
                ? 5
                : 7 * $ctx->prevEnvSignals('sig1', 1);
        });

        $model = new Model();
        $model = $model
                    ->withSignal('sig1', $sig1)
                    ->withSignal('sig2', $sig2);

        $this->assertEquals(2, $sig1->at(new Context(0, $sig1, $model)));
        $this->assertEquals(5, $sig2->at(new Context(0, $sig2, $model)));

        $this->assertEquals(15, $sig1->at(new Context(1, $sig1, $model)));
        $this->assertEquals(14, $sig2->at(new Context(1, $sig2, $model)));
    }
}
