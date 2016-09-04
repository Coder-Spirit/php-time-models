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

    public function __construct(Signal $left, Signal $right, int $cutPoint)
    {
        $this->left  = $left;
        $this->right = $right;
        $this->cutPoint = $cutPoint;
    }

    protected function _at(Context $ctx) : float
    {
        return ($ctx->getInstant() < $this->cutPoint)
            ? $this->left->_at($ctx)
            : $this->right->_at($ctx);
    }
}
