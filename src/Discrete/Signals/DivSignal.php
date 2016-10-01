<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Context\InstrumentedContext;


final class DivSignal extends ComposedSignal
{
    /** @var Signal */
    private $dividend;

    /** @var Signal */
    private $divisor;


    public function __construct(Signal $dividend, Signal $divisor)
    {
        $this->dividend = $dividend->getUncached();
        $this->divisor  = $divisor->getUncached();
    }

    protected function _at(InstrumentedContext $ctx) : float
    {
        return (
            $this->dividend->at($ctx->withSignal($this->dividend)) /
            $this->divisor->at($ctx->withSignal($this->divisor))
        );
    }
}
