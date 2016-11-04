<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Context\InstrumentedContext;


abstract class Signal
{
    /** @var array */
    private $cache = [];


    public function getUncached() : Signal
    {
        $signal = clone $this;
        $signal->cache = [];

        return $signal;
    }

    public function at(InstrumentedContext $ctx) : float
    {
        if (null === $ctx->getSignal()) {
            $ctx = $ctx->withSignal($this);
        }

        $ts = $ctx->getInstant();
        $cK = implode(',', $ctx->getDims());

        // We only use the cache if the context's signal matches this one.
        if ($this === $ctx->getSignal()) {
            if (!isset($this->cache[$ts][$cK])) {
                $this->cache[$ts][$cK] = $this->_at($ctx);
            }

            return $this->cache[$ts][$cK];
        }

        return $this->_at($ctx);
    }

    abstract protected function _at(InstrumentedContext $ctx) : float;
}
