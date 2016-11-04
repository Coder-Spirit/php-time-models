<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Context;


use Litipk\TimeModels\Discrete\Model;
use Litipk\TimeModels\Discrete\Signals\Signal;


final class SimpleContext implements InstrumentedContext
{
    /** @var int */
    private $t;

    /** @var int[] */
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
        $this->t      = $t;
        $this->signal = $signal;
        $this->model  = $model;
        $this->dims   = $dims;
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
        $ctx->t = $t;

        return $ctx;
    }

    public function withDims(int ...$dims) : InstrumentedContext
    {
        $ctx = clone $this;
        $ctx->dims = $dims;

        return $ctx;
    }

    public function getInstant() : int
    {
        return $this->t;
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

    /**
     * @return null|\Litipk\TimeModels\Discrete\Model
     */
    public function getModel()
    {
        return $this->model;
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
        $signal = $this->model->getSignal($signalName);
        return $signal->at(
            $this->getPastContext($stepsToPast, ...$dims)->withSignal($signal)
        );
    }

    private function getPastContext(int $stepsToPast, int ...$dims) : InstrumentedContext
    {
        if ($stepsToPast <= 0) throw new \InvalidArgumentException('Only positive values are allowed');

        $ctx = clone $this;
        $ctx->t -= $stepsToPast;
        $ctx->dims = $dims;

        return $ctx;
    }
}
