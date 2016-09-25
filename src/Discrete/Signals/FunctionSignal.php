<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Context\InstrumentedContext;


class FunctionSignal extends Signal
{
    /** @var callable */
    protected $func;


    /**
     * FunctionSignal constructor.
     * @param callable(int)|callable(int,Context) $func
     * @throws \TypeError
     */
    public function __construct(callable $func)
    {
        $reflectedFunc = new \ReflectionFunction($func);

        $this->validateCallableReturnType($reflectedFunc);
        $this->validateCallableParameters($reflectedFunc);

        $this->func = $func;
    }

    protected function _at(InstrumentedContext $ctx) : float
    {
        return (float)call_user_func_array(
            $this->func, array_merge([$ctx->getInstant()], $ctx->getDims(), [$ctx])
        );
    }

    private function validateCallableReturnType(\ReflectionFunction $reflectedFunc)
    {
        $reflectedReturn = $reflectedFunc->getReturnType();
        if (null === $reflectedReturn) {
            throw new \TypeError('The return type of the passed callable is missing');
        } elseif (!in_array($reflectedReturn->__toString(), ['float', 'int'])) {
            throw new \TypeError('The passed callable must return float values');
        }
    }

    protected function validateCallableParameters(\ReflectionFunction $reflectedFunc)
    {
        $reflectedParams = $reflectedFunc->getParameters();
        $nParams = count($reflectedParams);
        if (0 === $nParams) {
            throw new \TypeError('The passed callable must have at least the time as parameter');
        }

        for ($i = 0; $i < $nParams - 1; $i++) {
            $reflectedParam = $reflectedParams[$i];
            if (null === $reflectedParam->getType() || 'int' !== $reflectedParam->getType()->__toString()) {
                throw new \TypeError('All the callable parameters, except the last one, must be declared as integers');
            }
        }

        $reflectedParam = $reflectedParams[$nParams-1];
        $lastParamReflectedType = $reflectedParam->getType();
        if (
            null === $lastParamReflectedType                                                            ||
            $lastParamReflectedType->isBuiltin() && 'int' !== $lastParamReflectedType->__toString()     ||
            !$lastParamReflectedType->isBuiltin()                                                       &&
            $reflectedParam->getClass()->getNamespaceName() !== 'Litipk\\TimeModels\\Discrete\\Context'
        ) {
            throw new \TypeError('The callable\'s last parameter has to be declared as `int` or `Context`');
        }
    }
}
