<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete;


use Litipk\TimeModels\Discrete\Signals\Signal;


class SimpleContext implements InstrumentedContext
{
    /** @var int */
    private $instant;

    /** @var null|\Litipk\TimeModels\Discrete\Signals\Signal */
    private $signal;

    /** @var null|\Litipk\TimeModels\Discrete\Model */
    private $model;


    public function __construct(int $instant, array $dims = [], Model $model = null, Signal $signal = null)
    {
        $this->instant = $instant;
        $this->signal  = $signal;
        $this->model   = $model;
    }

    public function withSignal(Signal $signal) : Context
    {
        $ctx = clone $this;
        $ctx->signal = $signal;

        return $ctx;
    }

    public function getInstant() : int
    {
        return $this->instant;
    }

    /**
     * @return null|\Litipk\TimeModels\Discrete\Signals\Signal
     */
    public function getSignal()
    {
        return $this->signal;
    }

    public function prevSignal(int $stepsToPast) : float
    {
        return $this
            ->signal
            ->at($this->getPastContext($stepsToPast));
    }

    public function prevEnvSignals(string $signalName, int $stepsToPast) : float
    {
        return $this
            ->model
            ->getSignal($signalName)
            ->at($this->getPastContext($stepsToPast));
    }

    private function getPastContext(int $stepsToPast) : Context
    {
        if ($stepsToPast <= 0) throw new \InvalidArgumentException('Only positive values are allowed');

        $ctx = clone $this;
        $ctx->instant -= $stepsToPast;

        return $ctx;
    }
}
