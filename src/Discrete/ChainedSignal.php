<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete;


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

    protected function _at(Context $ctx) : float
    {
        return ($ctx->getInstant() < $this->cutPoint)
            ? $this->left->_at($ctx->withShift($this->leftShift))
            : $this->right->_at($ctx->withShift($this->rightShift));
    }
}
