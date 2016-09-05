<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete;


final class ConstantSignal extends Signal
{
    private $level;

    public function __construct(float $level)
    {
        $this->level = $level;
    }

    protected function _at(InstrumentedContext $ctx) : float
    {
        return $this->level;
    }
}
