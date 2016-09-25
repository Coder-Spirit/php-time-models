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
     * @throws \TypeError
     */
    public function __construct(callable $func)
    {
        $reflectedFunc = new \ReflectionFunction($func);

        $reflectedReturn = $reflectedFunc->getReturnType();
        if (null === $reflectedReturn) {
            throw new \TypeError('The return type of the passed callable is missing');
        } elseif (!in_array($reflectedReturn->__toString(), ['float', 'int'])) {
            throw new \TypeError('The passed callable must return float values');
        }

        $this->func = $func;
    }

    protected function _at(InstrumentedContext $ctx) : float
    {
        return (float)call_user_func_array(
            $this->func, array_merge([$ctx->getInstant()], $ctx->getDims(), [$ctx])
        );
    }
}
