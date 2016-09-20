<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


abstract class ComposedSignal extends Signal
{
    /** @var Signal[] */
    protected $signals;

    /**
     * @return Signal[]
     */
    public function getComponentSignals() : array
    {
        return $this->signals;
    }
}
