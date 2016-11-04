<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Context;


use Litipk\TimeModels\Discrete\Signal;


final class ShiftedContext implements InstrumentedContext
{
    /** @var InstrumentedContext */
    private $ctx;

    /** @var int */
    private $shift;

    public function __construct(InstrumentedContext $ctx, int $shift)
    {
        $this->ctx   = clone($ctx);
        $this->shift = $shift;
    }

    public function withSignal(Signal $signal) : InstrumentedContext
    {
        $ctx = clone $this;
        $ctx->ctx = $ctx->ctx->withSignal($signal);

        return $ctx;
    }

    public function withInstant(int $t) : InstrumentedContext
    {
        $ctx = clone $this;
        $ctx->ctx = $ctx->ctx->withInstant($t);

        return $ctx;
    }

    public function withDims(int ...$dims) : InstrumentedContext
    {
        $ctx = clone $this;
        $ctx->ctx = $ctx->ctx->withDims(...$dims);

        return $ctx;
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

    /**
     * @return null|\Litipk\TimeModels\Discrete\Model
     */
    public function getModel()
    {
        return $this->ctx->getModel();
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
}
