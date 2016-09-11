<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Tests\Discrete\Signal;


use Litipk\TimeModels\Discrete\Context\SimpleContext;
use Litipk\TimeModels\Discrete\Signals\ConstantSignal;
use Litipk\TimeModels\Discrete\Signals\FunctionSignal;
use Litipk\TimeModels\Discrete\Signals\SumSignal;


use PHPUnit\Framework\TestCase;


class SumSignalTest extends TestCase
{
    function testSum_two_functions_without_coeficients ()
    {
        $sig1 = new FunctionSignal(function (int $instant) {
            return $instant*2;
        });
        $sig2 = new FunctionSignal(function (int $instant) {
            return $instant*$instant;
        });

        $signal = new SumSignal($sig1, $sig2);

        $this->assertEquals(0.0, $signal->at(new SimpleContext(0)));
        $this->assertEquals(3.0, $signal->at(new SimpleContext(1)));
        $this->assertEquals(8.0, $signal->at(new SimpleContext(2)));
        $this->assertEquals(15.0, $signal->at(new SimpleContext(3)));
    }

    function testSum_three_functions_with_coeficients ()
    {
        $sig1 = new FunctionSignal(function (int $instant) {
            return $instant*2;
        });
        $sig2 = new FunctionSignal(function (int $instant) {
            return $instant*$instant;
        });
        $sig3 = new ConstantSignal(7.0);

        $signal = new SumSignal($sig1, $sig2, $sig3);

        $this->assertEquals(7.0, $signal->at(new SimpleContext(0)));
        $this->assertEquals(10.0, $signal->at(new SimpleContext(1)));
        $this->assertEquals(15.0, $signal->at(new SimpleContext(2)));
        $this->assertEquals(22.0, $signal->at(new SimpleContext(3)));
    }

    function testSum_two_functions_with_coeficients ()
    {
        $sig1 = new FunctionSignal(function (int $instant) {
            return $instant*2;
        });
        $sig2 = new FunctionSignal(function (int $instant) {
            return $instant*$instant;
        });

        $signal = new SumSignal($sig1, [3.0, $sig2]);

        $this->assertEquals(0.0, $signal->at(new SimpleContext(0)));
        $this->assertEquals(5.0, $signal->at(new SimpleContext(1)));
        $this->assertEquals(16.0, $signal->at(new SimpleContext(2)));
        $this->assertEquals(33.0, $signal->at(new SimpleContext(3)));
    }

    /**
     * @expectedException \TypeError
     */
    function testSum_with_invalid_params()
    {
        $sig1 = new FunctionSignal(function (int $instant) {
            return $instant*2;
        });
        $sig2 = new FunctionSignal(function (int $instant) {
            return $instant*$instant;
        });

        new SumSignal($sig1, [$sig2]);
    }
}
