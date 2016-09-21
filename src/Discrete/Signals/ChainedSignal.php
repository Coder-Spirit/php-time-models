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


    public function __construct(Signal $left, Signal $right, int $cutPoint, int $leftShift = 0, int $rightShift = 0)
    {
        $this->left  = (0 === $leftShift)
            ? $left
            : new TransformSignal($left, null, function (int $t) use ($leftShift) : int {
                return (int)($t + $leftShift);
            });
        $this->right = (0 === $rightShift)
            ? $right
            : new TransformSignal($right, null, function (int $t) use ($rightShift) : int {
                return (int)($t + $rightShift);
            });

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
            ? $this->left->_at($ctx)
            : $this->right->_at($ctx);
    }
}
