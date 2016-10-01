<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Context\InstrumentedContext;
use Litipk\TimeModels\Discrete\Context\SimpleContext;


class ScalarField1D extends FunctionSignal
{
    public function integrateUntilFirstZero(int $from=0) : ComposedSignal
    {
        return new class ($this, $from) extends ComposedSignal
        {
            /** @var ScalarField1D */
            private $field;

            /** @var int */
            private $from;

            public function __construct(ScalarField1D $field, int $from=0)
            {
                $this->field = $field;
                $this->from  = $from;
            }

            public function getComponentSignals() : array
            {
                return [$this->field];
            }

            protected function _at(InstrumentedContext $ctx) : float
            {
                $acc   = 0.0;
                $t     = $ctx->getInstant();
                $model = $ctx->getModel();

                $delta = $this->field->at(
                    new SimpleContext($t, [$this->from], $model, $this->field)
                );

                for ($i = $this->from + 1; $delta !== 0.0; $i++) {
                    $acc += $delta;

                    $delta = $this->field->at(
                        new SimpleContext($t, [$i], $model, $this->field)
                    );
                }

                return $acc;
            }
        };
    }

    public function withPatchedRegion(Signal $patch, callable $regionFilter, bool $keepField = false) : ComposedSignal
    {
        return new class ($this, $patch, $regionFilter, $keepField) extends ComposedSignal
        {
            /** @var ScalarField1D */
            private $base;

            /** @var Signal */
            private $patch;

            /** @var callable */
            private $regionFilter;

            /** @var bool */
            private $keepField;

            public function __construct(
                ScalarField1D $base, Signal $patch, callable $regionFilter, bool $keepField = false
            )
            {
                $this->base         = $base;
                $this->patch        = $patch;
                $this->regionFilter = $regionFilter;
                $this->keepField    = $keepField;
            }

            public function getComponentSignals() : array
            {
                return [$this->base, $this->patch];
            }

            protected function _at(InstrumentedContext $ctx) : float
            {
                if (($this->regionFilter)($ctx->getDims())) {
                    return $this->patch->at(
                        ($this->keepField)
                            ? $ctx
                            : $ctx->withDims([])
                    );
                } else {
                    return $this->base->at($ctx);
                }
            }
        };
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
