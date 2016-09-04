<?php
declare(strict_types=1);


namespace Litipk\MacPhply;


final class ConstantSignal extends Signal
{
    private $level;

    public function __construct(float $level)
    {
        $this->level = $level;
    }

    protected function _at(Context $ctx) : float
    {
        return $this->level;
    }
}
