<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Context\InstrumentedContext;


final class SumSignal extends ComposedSignal
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

    protected function _at(InstrumentedContext $ctx) : float
    {
        return (float)array_sum(array_map(function (float $coef, Signal $signal) use ($ctx) {
            return $coef*$signal->_at($ctx);
        }, $this->coefs, $this->signals));
    }
}
