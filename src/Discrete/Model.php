<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete;


use Litipk\TimeModels\Discrete\Context\SimpleContext;
use Litipk\TimeModels\Discrete\Signals\Signal;


final class Model
{
    /** @var array[string]Signal */
    private $signals = [];

    /** @var array[string]float */
    private $params = [];


    public function withSignal(string $signalName, Signal $signal) : Model
    {
        $model = clone $this;
        $model->signals[$signalName] = $signal->getUncached();

        return $model;
    }

    public function withParam(string $paramName, float $param) : Model
    {
        $model = clone $this;

        $model->params[$paramName] = $param;
        $model->signals = array_map(function (Signal $s) {
            return $s->getUncached();
        }, $model->signals);

        return $model;
    }

    public function getSignal(string $signalName) : Signal
    {
        return $this->signals[$signalName];
    }

    public function getParam(string $paramName) : float
    {
        return $this->params[$paramName];
    }

    public function eval(string $signalName, int $t, int ...$dims) : float
    {
        return $this->getSignal($signalName)->at(new SimpleContext($t, $dims, $this));
    }

    /**
     * @param string $signalName
     * @param int $since
     * @param int $until
     * @param \int[] ...$dims
     * @return float[]
     */
    public function evalTimeSlice(string $signalName, int $since, int $until, int ...$dims) : array
    {
        $signal = $this->getSignal($signalName);
        $ctx = new SimpleContext($since, $dims, $this);

        return array_map(function ($t) use ($signal, $ctx) {
            return $signal->at($ctx->withInstant($t));
        }, range($since, $until));
    }
}
