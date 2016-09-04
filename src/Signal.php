<?php


namespace Litipk\MacPhply;


abstract class Signal
{
    public function at(Context $ctx) : float
    {
        return $this->_at($ctx);
    }

    abstract protected function _at(Context $ctx) : float;
}
