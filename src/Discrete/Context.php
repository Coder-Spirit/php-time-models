<?php


namespace Litipk\TimeModels\Discrete;


interface Context
{
    public function getInstant() : int;

    public function prevSignal(int $stepsToPast) : float;

    public function prevEnvSignals(string $signalName, int $stepsToPast) : float;
}
