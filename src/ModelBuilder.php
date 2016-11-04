<?php
declare(strict_types=1);


namespace Litipk\TimeModels;


use Litipk\TimeModels\Exceptions\CyclicDependenceException;
use Litipk\TimeModels\Exceptions\InvalidReferenceException;
use Litipk\TimeModels\Exceptions\UnresolvedReferenceException;

use Litipk\TimeModels\Signals\ConstantSignal;
use Litipk\TimeModels\Signals\Signal;


final class ModelBuilder
{
    /** @var Signal[]|string[] Indexed by string keys  */
    private $signals = [];

    /** @var float[]|string[] Indexed by string keys */
    private $params = [];

    /**
     * @param string $signalName
     * @param string|Signal $signal
     * @return ModelBuilder
     */
    public function withSignal(string $signalName, $signal) : ModelBuilder
    {
        if ($signal instanceof Signal) {
            $this->signals[$signalName] = clone $signal;

            if ($signal instanceof ConstantSignal) {
                $this->params[$signalName] = $signal->getLevel();
            }
        } elseif (\is_string($signal)) {
            $this->signals[$signalName] = $signal;
        } else {
            throw new \InvalidArgumentException();
        }

        return $this;
    }

    /**
     * @param string $paramName
     * @param string|int|float $param
     * @return ModelBuilder
     */
    public function withParam(string $paramName, $param) : ModelBuilder
    {
        if (\is_string($param) || \is_int($param) || \is_float($param)) {
            if (\is_string($param) && \is_numeric($param)) {
                throw new \InvalidArgumentException('Numbers must be passed as int or float');
            }

            $this->params[$paramName] = $param;

            if (\is_numeric($param)) {
                $this->signals[$paramName] = new ConstantSignal($param);
            } else {
                $this->signals[$paramName] = $param;
            }
        } else {
            throw new \InvalidArgumentException();
        }

        return $this;
    }

    /**
     * @param string $sigName
     * @return string|Signal
     */
    public function getSignal(string $sigName)
    {
        if (isset($this->signals[$sigName])) {
            return $this->signals[$sigName];
        }

        throw new InvalidReferenceException($sigName);
    }

    /**
     * @param string $paramName
     * @return int|float|string
     */
    public function getParam(string $paramName)
    {
        if (isset($this->params[$paramName])) {
            return $this->params[$paramName];
        }

        throw new InvalidReferenceException($paramName);
    }

    /**
     * @return Model
     */
    public function build() : Model
    {
        $this->resolveParams();
        $this->resolveSignals();

        foreach ($this->signals as $signal) {
            $signal->resolveDependencies($this);
        }

        return new Model($this->signals);
    }

    private function resolveParams()
    {
        foreach ($this->params as $paramName => $param) {
            if (\is_string($param)) {
                $this->resolveParamByName($paramName);
            }
        }
    }

    private function resolveSignals()
    {
        foreach ($this->signals as $sigName => $signal) {
            if (\is_string($signal)) {
                $this->resolveSignalByName($sigName);
            }
            $this->signals[$sigName]->setName($sigName);
        }
    }

    private function resolveParamByName(string $paramName)
    {
        if (!isset($this->params[$paramName])) {
            throw new \LogicException('This method must be called on defined parameters');
        }

        $nIterations   = 0;
        $maxIterations = \count($this->params);
        $iterator      = $paramName;

        while (!\is_numeric($this->params[$iterator])) {
            $iterator = $this->params[$iterator];

            if (\is_string($iterator) && !isset($this->params[$iterator])) {
                throw new InvalidReferenceException($paramName);
            }
            elseif ($iterator === $paramName || $nIterations++ > $maxIterations) {
                throw new CyclicDependenceException();
            }
        }

        $this->params[$paramName] = $this->params[$iterator];
    }

    private function resolveSignalByName(string $sigName)
    {
        if (!isset($this->signals[$sigName])) {
            throw new \LogicException('This method must be called on defined parameters');
        }

        $nIterations   = 0;
        $maxIterations = \count($this->params);
        $iterator      = $sigName;

        while (!($this->signals[$iterator] instanceof Signal)) {
            $iterator = $this->signals[$iterator];

            if (\is_string($iterator) && !isset($this->signals[$iterator])) {
                throw new InvalidReferenceException($sigName);
            }
            elseif ($iterator === $sigName || $nIterations++ > $maxIterations) {
                throw new CyclicDependenceException();
            }
        }

        $this->signals[$sigName] = $this->signals[$iterator];
    }
}
