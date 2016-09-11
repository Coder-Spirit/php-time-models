<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Context;


use Litipk\TimeModels\Discrete\Signals\Signal;


final class ShiftedContext implements InstrumentedContext
{
    /** @var InstrumentedContext */
    private $ctx;

    /** @var int */
    private $shift;

    public function __construct(InstrumentedContext $ctx, int $shift)
    {
        $this->ctx   = $ctx;
        $this->shift = $shift;
    }

    public function getInstant() : int
    {
        return $this->ctx->getInstant() + $this->shift;
    }

    /** @return int[] */
    public function getDims() : array
    {
        return $this->ctx->getDims();
    }

    /**
     * @return null|\Litipk\TimeModels\Discrete\Signals\Signal
     */
    public function getSignal()
    {
        return $this->ctx->getSignal();
    }

    public function param(string $paramName) : float
    {
        return $this->ctx->param($paramName);
    }

    public function past(int $stepsToPast, int ...$dims) : float
    {
        return $this->ctx->past($stepsToPast, ...$dims);
    }

    public function globalPast(string $signalName, int $stepsToPast, int ...$dims) : float
    {
        return $this->ctx->globalPast($signalName, $stepsToPast, ...$dims);
    }

    public function withSignal(Signal $signal) : InstrumentedContext
    {
        $ctx = clone $this;
        $ctx->ctx = $ctx->ctx->withSignal($signal);

        return $ctx;
    }
}
