<?php
declare(strict_types=1);


namespace Litipk\TimeModels;


use Litipk\TimeModels\Context\Context;
use Litipk\TimeModels\Exceptions\InvalidReferenceException;
use Litipk\TimeModels\Signals\Signal;


final class Model
{
    /**
     * @var Signal[] Indexed by string keys
     */
    private $signals = [];

    /**
     * Model constructor.
     * @param Signal[] $signals
     */
    public function __construct(array $signals)
    {
        foreach ($signals as $sigName => $signal) {
            if (!\is_string($sigName) || !($signal instanceof Signal)) {
                throw new \InvalidArgumentException('Model requires an associative "dict" of Signal instances');
            }
        }

        $this->signals = $signals;

        foreach ($this->signals as $sigName => $signal) {
            $signal->install($this);
            $this->cache[$sigName] = [];
        }
    }

    public function eval(string $sigName, int $t, array $fieldPoint = null) : float
    {
        if (!isset($this->signals[$sigName])) {
            throw new InvalidReferenceException($sigName);
        }

        return $this->signals[$sigName]->at(new Context($t, $fieldPoint));
    }
}
