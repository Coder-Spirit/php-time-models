<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Context\InstrumentedContext;


final class MulSignal extends ComposedSignal
{
    public function __construct(Signal ... $signals)
    {
        $this->signals = array_map(function (Signal $s) { return $s->getUncached(); }, $signals);
    }

    protected function _at(InstrumentedContext $ctx) : float
    {
        return (float)array_reduce($this->signals, function ($carry, Signal $signal) use ($ctx) {
            return $carry * $signal->at($ctx);
        }, 1.0);
    }
}
