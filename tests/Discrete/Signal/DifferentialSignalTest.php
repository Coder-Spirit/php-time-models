<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Tests\Discrete\Signal;


use Litipk\TimeModels\Discrete\Context\SimpleContext;
use Litipk\TimeModels\Discrete\Signals\FunctionSignal;
use Litipk\TimeModels\Discrete\Signals\DifferentialSignal;

use PHPUnit\Framework\TestCase;


class DifferentialSignalTest extends TestCase
{
    function testMin ()
    {
        $sig1 = new FunctionSignal(function (int $instant) {
            return $instant*$instant;
        });

        $signal = new DifferentialSignal($sig1);

        $this->assertEquals(-3, $signal->at(new SimpleContext(-1)));
        $this->assertEquals(-1, $signal->at(new SimpleContext(0)));
        $this->assertEquals(1, $signal->at(new SimpleContext(1)));
        $this->assertEquals(3, $signal->at(new SimpleContext(2)));
        $this->assertEquals(5, $signal->at(new SimpleContext(3)));
        $this->assertEquals(7, $signal->at(new SimpleContext(4)));
    }
}
