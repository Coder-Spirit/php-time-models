<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Context\InstrumentedContext;


final class ByNameSignal extends Signal
{
    /** @var string */
    private $signalName;

    /** @var Signal */
    private $signal = null;


    public function __construct(string $signalName)
    {
        $this->signalName = $signalName;
    }

    public function getUncached() : Signal
    {
        $uncached = clone $this;
        if (null !== $uncached->signal) {
            $uncached->signal = $uncached->signal->getUncached();
        }

        return $uncached;
    }

    public function at(InstrumentedContext $ctx) : float
    {
        return ($this->signal ?? $this->getSignalFromModel($ctx))->at($ctx);
    }

    protected function _at(InstrumentedContext $ctx) : float
    {
        return ($this->signal ?? $this->getSignalFromModel($ctx))->at($ctx);
    }

    private function getSignalFromModel(InstrumentedContext $ctx) : Signal
    {
        $this->signal = $ctx->getModel()->getSignal($this->signalName);
        return $this->signal;
    }
}
