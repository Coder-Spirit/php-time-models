<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Exceptions;


class UnresolvedReferenceException extends \LogicException
{
    /** @var array */
    private $references;

    /**
     * UnresolvedReferenceException constructor.
     * @param array $references : string[string]
     */
    public function __construct(array $references)
    {
        parent::__construct('The signal cannot be evaluated because there are unresolved references');

        foreach ($references as $propName => $ref) {
            if (!is_string($propName) || !is_string($ref)) {
                throw new \InvalidArgumentException();
            }
        }

        $this->references = $references;
    }

    public function getUnresolvedReferences() : array
    {
        return $this->references;
    }
}
