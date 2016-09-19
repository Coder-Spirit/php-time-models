<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Context\InstrumentedContext;


final class ConstantSignal extends Signal
{
    private $level;

    public function __construct(float $level)
    {
        $this->level = $level;
    }

    public function getLevel() : float
    {
        return $this->level;
    }

    protected function _at(InstrumentedContext $ctx) : float
    {
        return $this->level;
    }
}
