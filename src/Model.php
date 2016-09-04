<?php
declare(strict_types=1);


namespace Litipk\MacPhply;


final class Model
{
    /** @var array */
    private $signals = [];


    public function withSignal(string $signalName, Signal $signal) : Model
    {
        $model = clone $this;
        $model->signals[$signalName] = $signal;

        return $model;
    }

    public function getSignal(string $signalName) : Signal
    {
        return $this->signals[$signalName];
    }
}
