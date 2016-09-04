<?php


namespace Litipk\TimeModels\Discrete;


interface Context
{
    public function withSignal(Signal $signal) : Context;

    public function getInstant() : int;

    /**
     * @return null|Signal
     */
    public function getSignal();

    public function prevSignal(int $stepsToPast) : float;

    public function prevEnvSignals(string $signalName, int $stepsToPast) : float;
}
