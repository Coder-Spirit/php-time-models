<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Tests\Discrete\Signal;


use Litipk\TimeModels\Discrete\Context\Context;
use Litipk\TimeModels\Discrete\Context\SimpleContext;
use Litipk\TimeModels\Discrete\Model;
use Litipk\TimeModels\Discrete\Signals\FunctionSignal;

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

        $this->assertEquals(0, $signal->at(new SimpleContext(0)));
        $this->assertEquals(2, $signal->at(new SimpleContext(1)));
        $this->assertEquals(4, $signal->at(new SimpleContext(2)));
    }

    public function testAt_AutoReferredFunction()
    {
        $signal = new FunctionSignal(function (int $instant, Context $ctx) {
            return ($instant <= 0)
                ? 1
                : 2 * $ctx->past(1);
        });

        $this->assertEquals(1, $signal->at(new SimpleContext(0)));
        $this->assertEquals(2, $signal->at(new SimpleContext(1)));
        $this->assertEquals(4, $signal->at(new SimpleContext(2)));
        $this->assertEquals(8, $signal->at(new SimpleContext(3)));
    }

    public function testAt_CrossReferredFunction()
    {
        $sig1 = new FunctionSignal(function (int $instant, Context $ctx) {
            return ($instant <= 0)
                ? 2
                : 3 * $ctx->globalPast('sig2', 1);
        });

        $sig2 = new FunctionSignal(function (int $instant, Context $ctx) {
            return ($instant <= 0)
                ? 5
                : 7 * $ctx->globalPast('sig1', 1);
        });

        $model = new Model();
        $model = $model
                    ->withSignal('sig1', $sig1)
                    ->withSignal('sig2', $sig2);

        $this->assertEquals(2, $sig1->at(new SimpleContext(0, [], $model)));
        $this->assertEquals(5, $sig2->at(new SimpleContext(0, [], $model)));

        $this->assertEquals(15, $sig1->at(new SimpleContext(1, [], $model)));
        $this->assertEquals(14, $sig2->at(new SimpleContext(1, [], $model)));
    }
}
