<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Context;


interface Context
{
    public function past(int $stepsToPast, array $dims = null) : float;

    public function globalPast(string $signalName, int $stepsToPast, array $dims = null) : float;
}
