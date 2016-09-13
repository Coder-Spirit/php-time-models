<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Context\InstrumentedContext;


final class TransformSignal extends Signal
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
     * @param null|callable $T      callable(float $signalValue, int $instant, int ...$dims):float
     * @param null|callable $timeT  callable(int $instant):int
     */
    public function __construct(Signal $signal, callable $T = null, callable $timeT = null)
    {
        $this->signal = $signal;
        $this->T      = (null !== $T)     ? $T     : function (float $x) : float { return $x; };
        $this->timeT  = (null !== $timeT) ? $timeT : function (int $x)   : int   { return $x; };
    }

    protected function _at(InstrumentedContext $ctx) : float
    {
        $localCtx = $ctx->withInstant(($this->timeT)($ctx->getInstant()));

        return (float)call_user_func_array(
            $this->T,
            array_merge([$this->signal->_at($localCtx), $localCtx->getInstant()], $localCtx->getDims())
        );
    }
}

