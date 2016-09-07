<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Tests\Discrete\Model;


use Litipk\TimeModels\Discrete\Context;
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

        $this->assertEquals(2, $model->evalSignal('sig1', 0));
        $this->assertEquals(5, $model->evalSignal('sig2', 0));

        $this->assertEquals(15, $model->evalSignal('sig1', 1));
        $this->assertEquals(14, $model->evalSignal('sig2', 1));
    }
}
