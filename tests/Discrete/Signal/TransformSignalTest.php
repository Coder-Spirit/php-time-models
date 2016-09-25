<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Tests\Discrete\Signal;


use Litipk\TimeModels\Discrete\Context\SimpleContext;
use Litipk\TimeModels\Discrete\Signals\FunctionSignal;
use Litipk\TimeModels\Discrete\Signals\TransformSignal;


use PHPUnit\Framework\TestCase;


class TransformSignalTest extends TestCase
{
    public function testSignalOutputTransformation ()
    {
        $sig1 = new FunctionSignal(function (int $instant) : float {
            return $instant*2;
        });
        $signal = new TransformSignal($sig1, function (float $o) : float {
            return $o*$o;
        });

        $this->assertEquals(0.0, $signal->at(new SimpleContext(0)));
        $this->assertEquals(4.0, $signal->at(new SimpleContext(1)));
        $this->assertEquals(16.0, $signal->at(new SimpleContext(2)));
        $this->assertEquals(36.0, $signal->at(new SimpleContext(3)));
    }

    public function testTimeTransformation ()
    {
        $sig1 = new FunctionSignal(function (int $instant) : float {
            return $instant*2;
        });
        $signal = new TransformSignal($sig1, null, function (int $t) : int {
            return -$t + 2;
        });

        $this->assertEquals(4.0, $signal->at(new SimpleContext(0)));
        $this->assertEquals(2.0, $signal->at(new SimpleContext(1)));
        $this->assertEquals(0.0, $signal->at(new SimpleContext(2)));
        $this->assertEquals(-2.0, $signal->at(new SimpleContext(3)));
    }
}
