<?php


namespace Litipk\MacPhply;


class Context
{
    /** @var int */
    private $instant;

    public function __construct(int $instant)
    {
        $this->instant = $instant;
    }

    public function getInstant() : int
    {
        return $this->instant;
    }
}
