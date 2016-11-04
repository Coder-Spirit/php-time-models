<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Tests\Discrete\Signal;


use Litipk\TimeModels\Discrete\Context\SimpleContext;
use Litipk\TimeModels\Discrete\Signals\FunctionSignal;
use Litipk\TimeModels\Discrete\Signals\MulSignal;


use PHPUnit\Framework\TestCase;


class MulSignalTest extends TestCase
{
    function testMul ()
    {
        $sig1 = new FunctionSignal(function (int $instant) : float {
            return $instant*2;
        });
        $sig2 = new FunctionSignal(function (int $instant) : float {
            return $instant*$instant;
        });

        $signal = new MulSignal($sig1, $sig2);

        $this->assertEquals(0.0, $signal->at(new SimpleContext(0)));
        $this->assertEquals(2.0, $signal->at(new SimpleContext(1)));
        $this->assertEquals(16.0, $signal->at(new SimpleContext(2)));
        $this->assertEquals(54.0, $signal->at(new SimpleContext(3)));
    }

    /**
     * @expectedException \TypeError
     */
    function testMul_with_invalid_params()
    {
        $sig1 = new FunctionSignal(function (int $instant) : float {
            return $instant*2;
        });
        $sig2 = new FunctionSignal(function (int $instant) : float {
            return $instant*$instant;
        });

        new MulSignal($sig1, [$sig2]);
    }
}
