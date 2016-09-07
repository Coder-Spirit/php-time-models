<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete;


use Litipk\TimeModels\Discrete\Signals\Signal;


interface InstrumentedContext extends Context
{
    public function withSignal(Signal $signal) : Context;

    public function getInstant() : int;

    /**
     * @return null|\Litipk\TimeModels\Discrete\Signals\Signal;
     */
    public function getSignal();
}
