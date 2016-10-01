<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Context\InstrumentedContext;


final class ChainedSignal extends ComposedSignal
{
    /** @var Signal */
    private $left;

    /** @var Signal */
    private $right;

    /** @var int */
    private $cutPoint;


    public function __construct(Signal $left, Signal $right, int $cutPoint)
    {
        $this->left  = $left->getUncached();
        $this->right = $right->getUncached();

        $this->cutPoint   = $cutPoint;
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
