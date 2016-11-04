<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Exceptions;


class InvalidReferenceException extends \LogicException
{
    public function __construct(string $reference)
    {
        parent::__construct("Invalid reference exception ($reference)");
    }
}
