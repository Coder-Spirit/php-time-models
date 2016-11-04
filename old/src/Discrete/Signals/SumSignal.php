<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Context\InstrumentedContext;
use Litipk\TimeModels\Discrete\Model;


final class SumSignal extends ComposedSignal implements ParametricSignal
{
    /** @var float[] */
    private $coefs;

    /**
     * @param (Signal|[float,Signal])[] $signals
     */
    public function __construct(... $signals)
    {
        $this->signals = array_map(function ($signal) {
            if ($signal instanceof Signal) {
                return $signal->getUncached();
            } elseif (is_array($signal) && 2 === count($signal) && $signal[1] instanceof Signal) {
                return $signal[1]->getUncached();
            } else {
                throw new \TypeError();
            }
        }, $signals);

        $this->coefs = array_map(function ($signal) {
            return ($signal instanceof Signal) ? 1.0 : $signal[0];
        }, $signals);
    }

    public function withParametersFromModel(Model $model) : Signal
    {
        $coefs = $this->coefs;

        $needToClone = false;
        foreach ($coefs as &$coef) {
            if (is_string($coef)) {
                $coef = $model->getParam($coef);
                $needToClone = true;
            }
        }

        if ($needToClone) {
            $signal = clone $this;
            $signal->coefs = $coefs;
        } else {
            $signal = $this;
        }

        return $signal;
    }

    protected function _at(InstrumentedContext $ctx) : float
    {
        return (float)array_sum(array_map(function (float $coef, Signal $signal) use ($ctx) {
            return $coef*$signal->at($ctx);
        }, $this->coefs, $this->signals));
    }
}
