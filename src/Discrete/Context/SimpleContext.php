<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Context;


use Litipk\TimeModels\Discrete\Model;
use Litipk\TimeModels\Discrete\Signals\Signal;


final class SimpleContext implements InstrumentedContext
{
    /** @var int */
    private $t;

    /** @var array */
    private $dims;

    /** @var null|\Litipk\TimeModels\Discrete\Signals\Signal */
    private $signal;

    /** @var null|\Litipk\TimeModels\Discrete\Model */
    private $model;


    /**
     * SimpleContext constructor.
     * @param int $t
     * @param int[] $dims
     * @param Model|null $model
     * @param Signal|null $signal
     */
    public function __construct(int $t, array $dims = [], Model $model = null, Signal $signal = null)
    {
        $this->instant = $t;
        $this->signal  = $signal;
        $this->model   = $model;
        $this->dims    = $dims;
    }

    public function withSignal(Signal $signal) : InstrumentedContext
    {
        $ctx = clone $this;
        $ctx->signal = $signal;

        return $ctx;
    }

    public function withInstant(int $t) : InstrumentedContext
    {
        $ctx = clone $this;
        $ctx->instant = $t;

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

    public function param(string $paramName) : float
    {
        return $this->model->getParam($paramName);
    }

    public function past(int $stepsToPast, int ...$dims) : float
    {
        return $this
            ->signal
            ->at($this->getPastContext($stepsToPast, ...$dims));
    }

    public function globalPast(string $signalName, int $stepsToPast, int ...$dims) : float
    {
        return $this
            ->model
            ->getSignal($signalName)
            ->at($this->getPastContext($stepsToPast, ...$dims));
    }

    private function getPastContext(int $stepsToPast, int ...$dims) : Context
    {
        if ($stepsToPast <= 0) throw new \InvalidArgumentException('Only positive values are allowed');

        $ctx = clone $this;
        $ctx->instant -= $stepsToPast;
        if (!empty($dims)) {
            $ctx->dims = $dims;
        }

        return $ctx;
    }
}
