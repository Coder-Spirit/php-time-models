<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Context\InstrumentedContext;


final class MinSignal extends ComposedSignal
{
    public function __construct(Signal ... $signals)
    {
        $this->signals = array_map(function (Signal $s) { return $s->getUncached(); }, $signals);
    }

    protected function _at(InstrumentedContext $ctx) : float
    {
        return (float)min(array_map(function (Signal $s) use ($ctx) {
            return $s->_at($ctx);
        }, $this->signals));
    }
}
