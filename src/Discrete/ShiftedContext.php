<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete;


class ShiftedContext implements Context
{
    /** @var Context */
    private $ctx;

    /** @var int */
    private $shift;

    public function __construct(Context $ctx, int $shift)
    {
        $this->ctx   = $ctx;
        $this->shift = $shift;
    }

    public function getInstant() : int
    {
        return $this->ctx->getInstant() + $this->shift;
    }

    /**
     * @return null|Signal
     */
    public function getSignal()
    {
        return $this->ctx->getSignal();
    }

    public function prevSignal(int $stepsToPast) : float
    {
        return $this->ctx->prevSignal($stepsToPast);
    }

    public function prevEnvSignals(string $signalName, int $stepsToPast) : float
    {
        return $this->ctx->prevEnvSignals($signalName, $stepsToPast);
    }

    public function withSignal(Signal $signal) : Context
    {
        $ctx = clone $this;
        $ctx->ctx = $ctx->ctx->withSignal($signal);

        return $ctx;
    }
}
