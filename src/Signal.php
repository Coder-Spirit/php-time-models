<?php
declare(strict_types=1);


namespace Litipk\MacPhply;


abstract class Signal
{
    /** @var array */
    private $cache = [];


    public function at(Context $ctx) : float
    {
        $instant = $ctx->getInstant();

        if (!isset($this->cache[$instant])) {
            $this->cache[$instant] = $this->_at($ctx);
        }

        return $this->cache[$instant];
    }

    abstract protected function _at(Context $ctx) : float;
}
