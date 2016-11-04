<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Context\InstrumentedContext;


final class DifferentialSignal extends ComposedSignal
{
    /** @var Signal */
    private $signal;


    public function __construct(Signal $signal)
    {
        $this->signal = $signal->getUncached();
    }

    /**
     * @return Signal[]
     */
    public function getComponentSignals() : array
    {
        return [$this->signal];
    }

    protected function _at(InstrumentedContext $ctx) : float
    {
        return (float)(
            $this->signal->at($ctx) -
            $this->signal->at(
                $ctx->withInstant($ctx->getInstant()-1)
            )
        );
    }

    protected function setComponentSignals(Signal ...$signals)
    {
        $this->signal = $signals[0];
    }
}
