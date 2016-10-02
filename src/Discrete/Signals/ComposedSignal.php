<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Model;


abstract class ComposedSignal extends Signal implements ParametricSignal
{
    /** @var Signal[] */
    protected $signals;

    public function withParametersFromModel(Model $model) : Signal
    {
        $signals = $this->getComponentSignals();

        $usedModelParameters = false;
        foreach ($signals as &$signalRef) {
            if ($signalRef instanceof ParametricSignal) {
                $parametrizedSignal = $signalRef->withParametersFromModel($model);
                if ($parametrizedSignal !== $signalRef) {
                    $signalRef = $parametrizedSignal;
                    $usedModelParameters = true;
                } else {
                    $signalRef = $signalRef->getUncached();
                }
            }
        }

        if ($usedModelParameters) {
            $parametrizedSignal = clone $this;
            $parametrizedSignal->setComponentSignals(...$signals);
        } else {
            return $this;
        }
    }

    /**
     * @return Signal[]
     */
    public function getComponentSignals() : array
    {
        return $this->signals;
    }

    protected function setComponentSignals(Signal ...$signals)
    {
        $this->signals = $signals;
    }
}
