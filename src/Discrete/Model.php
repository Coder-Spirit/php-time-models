<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete;


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
        $model->signals[$signalName] = $signal;

        return $model;
    }

    public function withParam(string $paramName, float $param) : Model
    {
        $model = clone $this;
        $model->params[$paramName] = $param;

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

    public function evalSignal(string $signalName, int $instant, array ...$dims) : float
    {
        return $this->getSignal($signalName)->at(new SimpleContext($instant, $dims, $this));
    }
}
