<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Context;


use Litipk\TimeModels\Discrete\Model;
use Litipk\TimeModels\Discrete\Signals\Signal;


class SimpleContext implements InstrumentedContext
{
    /** @var int */
    private $instant;

    /** @var array */
    private $dims;

    /** @var null|\Litipk\TimeModels\Discrete\Signals\Signal */
    private $signal;

    /** @var null|\Litipk\TimeModels\Discrete\Model */
    private $model;


    /**
     * SimpleContext constructor.
     * @param int $instant
     * @param int[] $dims
     * @param Model|null $model
     * @param Signal|null $signal
     */
    public function __construct(int $instant, array $dims = [], Model $model = null, Signal $signal = null)
    {
        $this->instant = $instant;
        $this->signal  = $signal;
        $this->model   = $model;
        $this->dims    = $dims;
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
     * @return int[]
     */
    public function getDims() : array
    {
        return $this->dims;
    }

    /**
     * @return null|\Litipk\TimeModels\Discrete\Signals\Signal
     */
    public function getSignal()
    {
        return $this->signal;
    }

    public function past(int $stepsToPast, array $dims = null) : float
    {
        return $this
            ->signal
            ->at($this->getPastContext($stepsToPast, $dims));
    }

    public function globalPast(string $signalName, int $stepsToPast, array $dims = null) : float
    {
        return $this
            ->model
            ->getSignal($signalName)
            ->at($this->getPastContext($stepsToPast, $dims));
    }

    private function getPastContext(int $stepsToPast, array $dims = null) : Context
    {
        if ($stepsToPast <= 0) throw new \InvalidArgumentException('Only positive values are allowed');

        $ctx = clone $this;
        $ctx->instant -= $stepsToPast;
        if (null !== $dims) {
            $ctx->dims = $dims;
        }

        return $ctx;
    }
}