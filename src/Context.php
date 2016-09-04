<?php
declare(strict_types=1);


namespace Litipk\MacPhply;


class Context
{
    /** @var int */
    private $instant;

    /** @var Signal */
    private $signal;


    public function __construct(int $instant, Signal $signal)
    {
        $this->instant = $instant;
        $this->signal  = $signal;
    }

    public function getInstant() : int
    {
        return $this->instant;
    }

    public function prevSignal(int $stepsToPast) : float
    {
        if ($stepsToPast <= 0) throw new \InvalidArgumentException('Only positive values are allowed');

        $ctx = clone $this;
        $ctx->instant -= $stepsToPast;

        return $this->signal->at($ctx);
    }
}
