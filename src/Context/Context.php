<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Context;


use Litipk\TimeModels\Signals\Signal;


final class Context
{
    /** @var int */
    private $t;

    /** @var null|array */
    private $fieldPoint;

    /** @var Signal */
    private $signal;

    public function __construct(int $t, array $fieldPoint = null, Signal $signal = null)
    {
        $this->t          = $t;
        $this->fieldPoint = $fieldPoint;
        $this->signal     = $signal;
    }

    public function getTime() : int
    {
        return $this->t;
    }

    public function getFieldPoint()
    {
        return $this->fieldPoint;
    }

    public function getSignal()
    {
        return $this->signal;
    }

    public function withSignal(Signal $signal) : Context
    {
        $ctx = clone $this;
        $ctx->signal = $signal;

        return $ctx;
    }
}
