<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Context\InstrumentedContext;
use Litipk\TimeModels\Discrete\Context\SimpleContext;


class ScalarField1D extends FunctionSignal
{
    public function integrateUntilFirstZero(int $from=0) : FunctionSignal
    {
        /** @var FunctionSignal $that */
        $that = $this;

        return new FunctionSignal(function (int $t, InstrumentedContext $ctx) use ($from, $that) : float {
            $acc   = 0.0;
            $model = $ctx->getModel();

            $localCtx = new SimpleContext($t, [$from], $model, $that);
            $delta = ($that->func)($t, $from, $localCtx);

            for ($i = $from + 1; $delta !== 0.0; $i++) {
                $acc += $delta;

                $localCtx = new SimpleContext($t, [$i], $model, $that);
                $delta = ($that->func)($t, $i, $localCtx);
            }

            return $acc;
        });
    }

    protected function validateCallableParameters(\ReflectionFunction $reflectedFunc)
    {
        $reflectedParams = $reflectedFunc->getParameters();
        $nParams = count($reflectedParams);
        if ($nParams < 2 || $nParams > 3) {
            throw new \TypeError('The passed callable must depend on the time variable AND on the 1D space coordinate');
        }

        for ($i = 0; $i < 2; $i++) {
            $reflectedParam = $reflectedParams[$i];
            if (
                null === $reflectedParam->getType() ||
                'int' !== $reflectedParam->getType()->__toString()
            ) {
                throw new \TypeError('All the callable parameters, except the last one, must be declared as integers');
            }
        }

        if (3 === $nParams) {
            $reflectedParam = $reflectedParams[2];
            $lastParamReflectedType = $reflectedParam->getType();
            if (
                null === $lastParamReflectedType                                                            ||
                $lastParamReflectedType->isBuiltin()                                                        ||
                !$lastParamReflectedType->isBuiltin()                                                       &&
                $reflectedParam->getClass()->getName() !== 'Litipk\\TimeModels\\Discrete\\Context\\Context'
            ) {
                throw new \TypeError('The callable\'s third parameter has to be declared as `Context`');
            }
        }
    }
}
