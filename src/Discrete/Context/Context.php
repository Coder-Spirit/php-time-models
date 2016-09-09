<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Context;


interface Context
{
    public function prevSignal(int $stepsToPast) : float;

    public function prevEnvSignals(string $signalName, int $stepsToPast) : float;
}
