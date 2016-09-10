<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Context;


use Litipk\TimeModels\Discrete\Signals\Signal;


interface InstrumentedContext extends Context
{
    public function withSignal(Signal $signal) : InstrumentedContext;

    public function getInstant() : int;

    /** @return int[] */
    public function getDims() : array;

    /**
     * @return null|\Litipk\TimeModels\Discrete\Signals\Signal;
     */
    public function getSignal();
}
