<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Tests\Discrete\Signal;


use Litipk\TimeModels\Discrete\Context\Context;
use Litipk\TimeModels\Discrete\Context\SimpleContext;
use Litipk\TimeModels\Discrete\Model;
use Litipk\TimeModels\Discrete\Signals\ByNameSignal;
use Litipk\TimeModels\Discrete\Signals\ChainedSignal;
use Litipk\TimeModels\Discrete\Signals\ConstantSignal;
use Litipk\TimeModels\Discrete\Signals\FunctionSignal;

use PHPUnit\Framework\TestCase;


class ByNameSignalTest extends TestCase
{
    public function testByNameFeature_simplestCase () {
        $sig1 = new ConstantSignal(84.0);
        $sig2 = new ByNameSignal('sig1');

        $model = (new Model())->withSignal('sig1', $sig1);

        $this->assertEquals(84.0, $sig2->at(new SimpleContext(0, [], $model)));
    }

    public function testProtected_at () {
        $sig1 = new ConstantSignal(84.0);
        $sig2 = new ConstantSignal(168.0);

        $sig3 = new ByNameSignal('sig1');
        $sig4 = new ByNameSignal('sig2');

        $sig5 = new ChainedSignal($sig3, $sig4, 5);

        $model = (new Model())
            ->withSignal('sig1', $sig1)
            ->withSignal('sig2', $sig2);

        $this->assertEquals(84.0, $sig5->at(new SimpleContext(0, [], $model)));
        $this->assertEquals(168.0, $sig5->at(new SimpleContext(10, [], $model)));
    }

    /**
     * @expectedException \Litipk\TimeModels\Exceptions\CyclicDependenceException
     */
    public function test_autoReference()
    {
        $sig1 = new ByNameSignal('sig1');
        (new Model())->withSignal('sig1', $sig1);
    }

    /**
     * @expectedException \Litipk\TimeModels\Exceptions\InvalidReferenceException
     */
    public function test_invalidReference()
    {
        $sig1 = new ByNameSignal('sigX');
        (new Model())->withSignal('sig1', $sig1);
    }

    /**
     * @expectedException \Litipk\TimeModels\Exceptions\CyclicDependenceException
     */
    public function test_longReferencesCycle()
    {
        $sig1a = new ConstantSignal(42);
        $sig2  = new ByNameSignal('sig1');
        $sig3  = new ByNameSignal('sig2');
        $sig1b = new ByNameSignal('sig3');

        (new Model())
            ->withSignal('sig1', $sig1a)
            ->withSignal('sig2', $sig2)
            ->withSignal('sig3', $sig3)
            ->withSignal('sig1', $sig1b);
    }
}
