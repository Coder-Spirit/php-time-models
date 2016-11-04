<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Context\InstrumentedContext;
use Litipk\TimeModels\Discrete\Context\ShiftedContext;


final class TransformSignal extends ComposedSignal
{
    /** @var Signal */
    private $signal;

    /** @var callable */
    private $T;

    /** @var callable */
    private $timeT;


    /**
     * TransformSignal constructor.
     * @param Signal $signal
     * @param null|callable $T      callable(float $signalValue, int $t, int ...$dims):float
     * @param null|callable $timeT  callable(int $t):int
     */
    public function __construct(Signal $signal, callable $T = null, callable $timeT = null)
    {
        $this->signal = $signal->getUncached();
        $this->T      = (null !== $T)     ? $T     : function (float $x) : float { return $x; };
        $this->timeT  = (null !== $timeT) ? $timeT : function (int $x)   : int   { return $x; };
    }

    protected function _at(InstrumentedContext $ctx) : float
    {
        $t        = $ctx->getInstant();
        $t2       = ($this->timeT)($t);
        $shift    = $t2-$t;
        $localCtx = ($shift !== 0) ? new ShiftedContext($ctx, $shift) : $ctx;

        return (float)call_user_func_array(
            $this->T,
            array_merge([$this->signal->at($localCtx), $t2], $localCtx->getDims())
        );
    }

    /**
     * @return Signal[]
     */
    public function getComponentSignals() : array
    {
        return [$this->signal];
    }
}

