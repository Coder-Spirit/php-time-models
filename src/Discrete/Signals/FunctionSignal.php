<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Context\InstrumentedContext;


final class FunctionSignal extends Signal
{
    /** @var callable */
    private $func;


    /**
     * FunctionSignal constructor.
     * @param callable(int)|callable(int,Context) $func
     */
    public function __construct(callable $func)
    {
        // TODO: Check $func signature
        $this->func = $func;
    }

    protected function _at(InstrumentedContext $ctx) : float
    {
        return ($this->func)($ctx->getInstant(), $ctx);
    }
}
