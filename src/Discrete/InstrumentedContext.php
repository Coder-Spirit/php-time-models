<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete;


interface InstrumentedContext extends Context
{
    public function withSignal(Signal $signal) : Context;

    public function getInstant() : int;

    /**
     * @return null|Signal
     */
    public function getSignal();
}
