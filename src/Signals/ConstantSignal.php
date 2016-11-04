<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Signals;


use Litipk\TimeModels\Context\Context;


final class ConstantSignal extends Signal
{
    protected static $SIGNAL_PARAMS = ['level'];

    /**
     * ConstantSignal constructor.
     * @param string|float $level
     * @param null|int $nFieldDims
     * @param null|int $startPoint
     * @param array    $fieldDims
     */
    public function __construct($level, int $startPoint = null, int $nFieldDims = null, array $fieldDims = [])
    {
        parent::__construct(
            $startPoint ?? -PHP_INT_MAX,
            $nFieldDims,
            $fieldDims
        );

        $this->setFloatParam('level', $level);
    }

    /**
     * @return string|float
     */
    public function getLevel()
    {
        return $this->params['level'];
    }

    /**
     * @param Context $ctx
     * @return float
     */
    protected function _at(Context $ctx) : float
    {
        return $this->params['level'];
    }
}
