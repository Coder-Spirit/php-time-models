<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Context\InstrumentedContext;


abstract class Signal
{
    /** @var array */
    private $cache = [];


    public function at(InstrumentedContext $ctx) : float
    {
        if (null === $ctx->getSignal()) {
            $ctx = $ctx->withSignal($this);
        }

        $instant = $ctx->getInstant();

        // We only use the cache if the context's signal matches this one.
        if ($this === $ctx->getSignal()) {
            if (!isset($this->cache[$instant])) {
                $this->cache[$instant] = $this->_at($ctx);
            }

            return $this->cache[$instant];
        }

        return $this->_at($ctx);
    }

    abstract protected function _at(InstrumentedContext $ctx) : float;
}
