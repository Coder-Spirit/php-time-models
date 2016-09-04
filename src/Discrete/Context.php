<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete;


final class Context
{
    /** @var int */
    private $instant;

    /** @var int */
    private $shift;

    /** @var null|Signal */
    private $signal;

    /** @var null|Model */
    private $model;


    public function __construct(int $instant, Signal $signal = null, Model $model = null, int $shift = 0)
    {
        $this->instant = $instant;
        $this->signal  = $signal;
        $this->model   = $model;
        $this->shift   = 0;
    }

    public function withShift(int $shift) : Context
    {
        $ctx = clone $this;
        $ctx->shift = $shift;

        return $ctx;
    }

    public function getInstant() : int
    {
        return $this->instant + $this->shift;
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
