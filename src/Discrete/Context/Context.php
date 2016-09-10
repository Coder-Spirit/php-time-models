<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Context;


interface Context
{
    public function param(string $paramName) : float;

    public function past(int $stepsToPast, ...$dims) : float;

    public function globalPast(string $signalName, int $stepsToPast, ...$dims) : float;
}
