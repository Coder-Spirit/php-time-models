<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Tests\Discrete\Signal;


use Litipk\TimeModels\Discrete\Context\SimpleContext;
use Litipk\TimeModels\Discrete\Signals\ScalarField1D;


use PHPUnit\Framework\TestCase;


class ScalarField1DTest extends TestCase
{
    /**
     * @expectedException \TypeError
     * @expectedExceptionMessage The callable's third parameter has to be declared as `Context`
     */
    function testConstructor_withCallableWithUntypedThirdParameter()
    {
        new ScalarField1D(function (int $t, int $d1, $ctx) : float {
            return max(0, $t-$d1);
        });
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessage The callable's third parameter has to be declared as `Context`
     */
    function testConstructor_withCallableWithIncorrectlyTypedThirdParameter_1()
    {
        new ScalarField1D(function (int $t, int $d1, int $ctx) : float {
            return max(0, $t-$d1);
        });
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessage The callable's third parameter has to be declared as `Context`
     */
    function testConstructor_withCallableWithIncorrectlyTypedThirdParameter_2()
    {
        new ScalarField1D(function (int $t, int $d1, ScalarField1D $ctx) : float {
            return max(0, $t-$d1);
        });
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessage The passed callable must depend on the time variable AND on the 1D space coordinate
     */
    function testConstructor_withCallableWithTooManyParameters()
    {
        new ScalarField1D(function (int $t, int $d1, int $d2, int $d3) : float {
            return max(0, $t + $d1*$d2*$d3);
        });
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessage The passed callable must depend on the time variable AND on the 1D space coordinate
     */
    function testConstructor_withCallableWithTooFewParameters()
    {
        new ScalarField1D(function (int $t) : float {
            return max(0, $t);
        });
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessage All the callable parameters, except the last one, must be declared as integers
     */
    function testConstructor_withCallableWithUntypedTimeParameter()
    {
        new ScalarField1D(function ($t, int $d1) : float {
            return max(0, $t - $d1);
        });
    }

    function testIntegrateUntilFirstZero ()
    {
        $sig1 = new ScalarField1D(function (int $t, int $d1) : float {
            return max(0, $t-$d1);
        });
        $sig2 = $sig1->integrateUntilFirstZero();
        $sig3 = $sig1->integrateUntilFirstZero(1);

        $this->assertEquals(0.0, $sig2->at(new SimpleContext(0)));
        $this->assertEquals(1.0, $sig2->at(new SimpleContext(1)));
        $this->assertEquals(3.0, $sig2->at(new SimpleContext(2)));
        $this->assertEquals(6.0, $sig2->at(new SimpleContext(3)));
        $this->assertEquals(10.0, $sig2->at(new SimpleContext(4)));

        $this->assertEquals(0.0, $sig3->at(new SimpleContext(1)));
        $this->assertEquals(1.0, $sig3->at(new SimpleContext(2)));
        $this->assertEquals(3.0, $sig3->at(new SimpleContext(3)));
        $this->assertEquals(6.0, $sig3->at(new SimpleContext(4)));
        $this->assertEquals(10.0, $sig3->at(new SimpleContext(5)));
    }
}
