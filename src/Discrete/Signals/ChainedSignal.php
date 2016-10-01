<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Context\InstrumentedContext;
use Litipk\TimeModels\Discrete\Model;


final class ChainedSignal extends ComposedSignal implements ParametricSignal
{
    /** @var Signal */
    private $left;

    /** @var Signal */
    private $right;

    /** @var int */
    private $cutPoint;


    /**
     * ChainedSignal constructor.
     * @param Signal $left
     * @param Signal $right
     * @param int|string $cutPoint
     */
    public function __construct(Signal $left, Signal $right, $cutPoint)
    {
        $this->left  = $left->getUncached();
        $this->right = $right->getUncached();

        $this->cutPoint = $cutPoint;
    }

    public function withParametersFromModel(Model $model) : Signal
    {
        if (is_string($this->cutPoint)) {
            $signal = clone $this;
            $signal->cutPoint = (int)$model->getParam($this->cutPoint);
        } else {
            $signal = $this;
        }

        return $signal;
    }

    /**
     * @return Signal[]
     */
    public function getComponentSignals() : array
    {
        return [$this->left, $this->right];
    }

    protected function _at(InstrumentedContext $ctx) : float
    {
        return ($ctx->getInstant() < $this->cutPoint)
            ? $this->left->at($ctx)
            : $this->right->at($ctx);
    }
}
