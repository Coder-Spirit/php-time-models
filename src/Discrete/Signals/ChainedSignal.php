<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\InstrumentedContext;
use Litipk\TimeModels\Discrete\ShiftedContext;


final class ChainedSignal extends Signal
{
    /** @var Signal */
    private $left;

    /** @var Signal */
    private $right;

    /** @var int */
    private $cutPoint;

    /** @var int */
    private $leftShift;

    /** @var int */
    private $rightShift;

    public function __construct(Signal $left, Signal $right, int $cutPoint, int $leftShift = 0, int $rightShift = 0)
    {
        $this->left  = $left;
        $this->right = $right;

        $this->cutPoint   = $cutPoint;
        $this->leftShift  = $leftShift;
        $this->rightShift = $rightShift;
    }

    protected function _at(InstrumentedContext $ctx) : float
    {
        return ($ctx->getInstant() < $this->cutPoint)
            ? $this->left->_at(new ShiftedContext($ctx, $this->leftShift))
            : $this->right->_at(new ShiftedContext($ctx, $this->rightShift));
    }
}
