<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete;


use Litipk\TimeModels\Discrete\Context\SimpleContext;
use Litipk\TimeModels\Discrete\Signals\ByNameSignal;
use Litipk\TimeModels\Discrete\Signals\ComposedSignal;
use Litipk\TimeModels\Discrete\Signals\ConstantSignal;
use Litipk\TimeModels\Discrete\Signals\ParametricSignal;
use Litipk\TimeModels\Discrete\Signals\Signal;
use Litipk\TimeModels\Exceptions\CyclicDependenceException;
use Litipk\TimeModels\Exceptions\InvalidReferenceException;


final class Model
{
    /** @var array[string]Signal */
    private $signals = [];

    /** @var array[string]float */
    private $params = [];


    public function withSignal(string $signalName, Signal $signal) : Model
    {
        if ($signal instanceof ParametricSignal) {
            $signal = $signal->withParametersFromModel($this);
        }

        $model = clone $this;
        $model->signals[$signalName] = $signal->getUncached();

        if ($signal instanceof ConstantSignal) {
            $model->params[$signalName] = $signal->getLevel();
        } elseif ($signal instanceof ByNameSignal || $signal instanceof ComposedSignal) {
            $this->validateSignalsReferences($signalName, $signal, $model);
        }

        return $model;
    }

    public function withParam(string $paramName, float $param) : Model
    {
        $model = clone $this;

        $model->params[$paramName] = $param;

        $model->signals = array_map(function (Signal $s) {
            return $s->getUncached();
        }, $model->signals);
        $model->signals[$paramName] = new ConstantSignal($param);

        return $model;
    }

    public function getSignal(string $signalName) : Signal
    {
        return $this->signals[$signalName];
    }

    public function getParam(string $paramName) : float
    {
        return $this->params[$paramName];
    }

    public function eval(string $signalName, int $t, int ...$dims) : float
    {
        return $this->getSignal($signalName)->at(new SimpleContext($t, $dims, $this));
    }

    /**
     * @param string $signalName
     * @param int $since
     * @param int $until
     * @param \int[] ...$dims
     * @return float[]
     */
    public function evalTimeSlice(string $signalName, int $since, int $until, int ...$dims) : array
    {
        $signal = $this->getSignal($signalName);
        $ctx = new SimpleContext($since, $dims, $this);

        return array_map(function ($t) use ($signal, $ctx) {
            return $signal->at($ctx->withInstant($t));
        }, range($since, $until));
    }

    /**
     * @param string $signalName
     * @param Signal $signal
     * @param Model $model
     * @throws CyclicDependenceException|InvalidReferenceException
     */
    private function validateSignalsReferences(string $signalName, Signal $signal, Model $model)
    {
        $numSignals = count($model->signals);
        $signalsQueue = [[$signal, 0]];

        while (!empty($signalsQueue)) {
            $tmpSignal = array_pop($signalsQueue);
            /** @var int $refsCount */
            $refsCount = $tmpSignal[1];
            /** @var Signal $tmpSignal */
            $tmpSignal = $tmpSignal[0];

            if ($tmpSignal instanceof ComposedSignal) {
                $signalsQueue = array_merge(
                    $signalsQueue,
                    array_map(
                        function ($s) use ($refsCount) {
                            return [$s, $refsCount];
                        },
                        $tmpSignal->getComponentSignals()
                    )
                );
            } elseif ($tmpSignal instanceof ByNameSignal) {
                if ($tmpSignal->getReferredSignalName() === $signalName || $refsCount >= $numSignals) {
                    throw new CyclicDependenceException();
                }
                if (!isset($this->signals[$tmpSignal->getReferredSignalName()])) {
                    throw new InvalidReferenceException();
                }

                $signalsQueue[] = [
                    $this->signals[$tmpSignal->getReferredSignalName()],
                    $refsCount + 1
                ];
            }
        }
    }
}
