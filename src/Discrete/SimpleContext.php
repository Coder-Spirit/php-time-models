<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete;


class SimpleContext implements Context
{
    /** @var int */
    private $instant;

    /** @var null|Signal */
    private $signal;

    /** @var null|Model */
    private $model;


    public function __construct(int $instant, Signal $signal = null, Model $model = null)
    {
        $this->instant = $instant;
        $this->signal  = $signal;
        $this->model   = $model;
    }

    public function getInstant() : int
    {
        return $this->instant;
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
