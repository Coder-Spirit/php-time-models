<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Tests\Discrete\Model;


use Litipk\TimeModels\Discrete\Context\Context;
use Litipk\TimeModels\Discrete\Model;
use Litipk\TimeModels\Discrete\Signals\FunctionSignal;

use PHPUnit\Framework\TestCase;


class ModelTest extends TestCase
{
    public function testClassExistence()
    {
        $model = new Model();
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

        $this->assertEquals(2, $model->eval('sig1', 0));
        $this->assertEquals(5, $model->eval('sig2', 0));

        $this->assertEquals(15, $model->eval('sig1', 1));
        $this->assertEquals(14, $model->eval('sig2', 1));
    }

    public function testAt_ScalarField()
    {
        $sig = new FunctionSignal(function (int $instant, int $shift) {
            return sin($instant + 2*$shift);
        });
        $model = new Model();
        $model = $model
            ->withSignal('sig', $sig);

        $this->assertEquals(sin(0), $model->eval('sig', 0, 0));
        $this->assertEquals(sin(2), $model->eval('sig', 0, 1));
        $this->assertEquals(sin(1), $model->eval('sig', 1, 0));
        $this->assertEquals(sin(3), $model->eval('sig', 1, 1));
    }

    public function testWithParam()
    {
        $sig = new FunctionSignal(function (int $instant, int $shift, Context $ctx) {
            return $instant * $ctx->param('p1') + $shift * $ctx->param('p2');
        });
        $model = (new Model())
            ->withSignal('sig', $sig)
            ->withParam('p1', 2)
            ->withParam('p2', 3);

        $this->assertEquals(0, $model->eval('sig', 0, 0));
        $this->assertEquals(3, $model->eval('sig', 0, 1));
        $this->assertEquals(2, $model->eval('sig', 1, 0));
        $this->assertEquals(5, $model->eval('sig', 1, 1));
    }

    public function testEvalTimeSlice()
    {
        $signal = new FunctionSignal(function (int $instant, Context $ctx) {
            return ($instant <= 0)
                ? 1
                : 2 * $ctx->past(1);
        });
        $model = (new Model())->withSignal('sig', $signal);

        $this->assertEquals(
            [1.0, 2.0, 4.0, 8.0],
            $model->evalTimeSlice('sig', 0, 3)
        );
    }

    public function testUnsharedModel()
    {
        $sig = new FunctionSignal(function (int $instant, int $shift, Context $ctx) {
            return $instant * $ctx->param('p1') + $shift * $ctx->param('p2');
        });
        $m1 = (new Model())
            ->withParam('p1', 2)
            ->withParam('p2', 3)
            ->withSignal('sig', $sig);

        $m2 = (new Model())
            ->withParam('p1', 5)
            ->withParam('p2', 7)
            ->withSignal('sig', $sig);

        $this->assertEquals(0, $m1->eval('sig', 0, 0));
        $this->assertEquals(3, $m1->eval('sig', 0, 1));
        $this->assertEquals(2, $m1->eval('sig', 1, 0));
        $this->assertEquals(5, $m1->eval('sig', 1, 1));

        $this->assertEquals(0, $m2->eval('sig', 0, 0));
        $this->assertEquals(7, $m2->eval('sig', 0, 1));
        $this->assertEquals(5, $m2->eval('sig', 1, 0));
        $this->assertEquals(12, $m2->eval('sig', 1, 1));
    }
}
