<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Signals;


use Litipk\TimeModels\Context\Context;
use Litipk\TimeModels\Exceptions\InfiniteTimeRecursionException;
use Litipk\TimeModels\Model;
use Litipk\TimeModels\Exceptions\UnresolvedReferenceException;
use Litipk\TimeModels\ModelBuilder;


abstract class Signal
{
    protected static $SIGNAL_PARAMS = [];
    protected static $SIGNAL_PIECES = [];

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $cache;

    /**
     * @var string
     */
    protected $name;

    /**
     * Defines the time point where the signal starts being defined.
     * @var null|int
     */
    protected $startPoint;

    /**
     * Defines how many dimensions has the field (field as in physics) represented by the signal.
     *   - null : undefined, for constants over the whole model
     *   - 0    : for scalar signals
     *   - 1    : for "line" signals
     *   - 2    : for signals computed over 2-dimensional fields (heat maps, for example).
     * @var null|int
     */
    protected $nFieldDims;

    /**
     * Defines the field dimensions, some examples:
     *   - 0/undef dims : []          -> nothing interesting, we have scalars, or constants over the whole model
     *   - 1 dimension  : [[0, null]] -> line starting at 0, until infinite
     *   - 1 dimension  : [[null, 0]] -> line starting at -INF, until 0
     *   - 2 dims       : [[null, null],[null, null]] -> the infinite plane
     *   - 2 dims       : [[0, null], [0, null]] -> The "first quadrant" of the plane
     * @var array[] : (int|null)[][]
     */
    protected $fieldDims;

    /**
     * The constructor fills it with references to the "non-inherited" properties (numeric parameters)
     * @var array : (string|float)[]
     */
    protected $params = [];

    /**
     * @var string[]
     */
    protected $paramTypes = [];

    /**
     * The constructor fills it with references to the "non-inherited" properties (Signals used to compose this one)
     * @var array : (string|Signal)[]
     */
    protected $sigPieces = [];

    /**
     * @var bool
     */
    protected $resolvedDependencies = true;

    /**
     * Signal constructor.
     * @param int|null $startPoint
     * @param int $nFieldDims
     * @param array[] $fieldDims
     */
    protected function __construct(int $startPoint = null, int $nFieldDims = null, array $fieldDims = [])
    {
        $this->startPoint = $startPoint;
        $this->nFieldDims = $nFieldDims;

        if ($nFieldDims > 0 && \count($fieldDims) !== $nFieldDims) {
            throw new \InvalidArgumentException('Number of dimensions mismatch');
        }

        foreach ($fieldDims as $dimDef) {
            if (
                !\is_array($dimDef) || 2 !== \count($dimDef)                           ||
                null !== $dimDef[0] && !\is_int($dimDef[0])                            ||
                null !== $dimDef[1] && !\is_int($dimDef[1])                            ||
                \is_int($dimDef[0]) && \is_int($dimDef[1]) && $dimDef[1] <= $dimDef[0]
            ) {
                throw new \InvalidArgumentException('Invalid field dimensions specification');
            }
        }

        $this->fieldDims  = $nFieldDims ? $fieldDims : [];
        $this->cache      = [];
    }

    /**
     * @param Context $ctx
     * @return float
     */
    public function at(Context $ctx) : float
    {
        if (!$this->resolvedDependencies) {
            $this->throwUnresolvedReference();
        }

        if (null === $ctx->getSignal()) {
            $ctx = $ctx->withSignal($this);
        }

        $t          = $ctx->getTime();
        $fieldPoint = $ctx->getFieldPoint();

        if (null !== $this->startPoint && $t < $this->startPoint) {
            throw new \DomainException();
        }

        if ($this->nFieldDims === null) {
            $fieldPoint = null;
        } elseif ($this->nFieldDims > 0 && (!\is_array($fieldPoint) || \count($fieldPoint) !== $this->nFieldDims)) {
            throw new \InvalidArgumentException('Dimensions mismatch');
        } elseif ($this->nFieldDims === 0 && null !== $fieldPoint) {
            throw new \InvalidArgumentException('Dimensions mismatch');
        }

        if (null === $fieldPoint) {
            if (!isset($this->cache[$t])) {
                $this->cache[$t] = $this->_at($ctx);
            }

            return $this->cache[$t];
        } else {
            $eFp = json_encode($fieldPoint);
            if (!isset($this->cache[$t][$eFp])) {
                $this->cache[$t][$eFp] = $this->_at($ctx);
            }

            return $this->cache[$t][$eFp];
        }
    }

    /**
     * Tells us if the signal can be directly "installed" inside the model.
     * @return bool
     */
    public function canBeSeed() : bool
    {
        return ($this->startPoint !== null);
    }

    /**
     * This method fixes the signal name inside the model
     * @param string $name
     */
    public function setName(string $name)
    {
        if (!$this->canBeSeed()) {
            throw new InfiniteTimeRecursionException('The current signal cannot be directly computed');
        }

        $this->_setName($name);
    }

    /**
     * @param Model $model
     */
    public function install(Model $model)
    {
        if (!$this->canBeSeed()) {
            throw new InfiniteTimeRecursionException('The current signal cannot be directly computed');
        }

        $this->_install($model);
    }

    /**
     * @param ModelBuilder $mb
     */
    public function resolveDependencies(ModelBuilder $mb)
    {
        if ($this->resolvedDependencies) {
            return;
        }

        $this->resolveParamDependencies($mb);
        $this->resolvePiecesDependencies($mb);

        $this->resolvedDependencies = true;
    }

    /**
     *
     */
    public function __clone()
    {
        $this->cache = [];

        foreach ($this->sigPieces as $sigName => $sigPiece) {
            if ($sigPiece instanceof Signal) {
                $this->sigPieces[$sigName] = clone $sigPiece;
            }
        }
    }

    /**
     * @param string $name
     */
    protected function _setName(string $name)
    {
        $this->name  = $name;

        foreach ($this->sigPieces as $sigName => $sigPiece) {
            if ($sigPiece instanceof Signal) {
                $sigPiece->_setName($name . '.' . $sigName);
            }
        }
    }

    /**
     * @param Model $model
     */
    protected function _install(Model $model)
    {
        if (!$this->resolvedDependencies) {
            $this->throwUnresolvedReference();
        }

        $this->model = $model;

        /** @var Signal $sigPiece */
        foreach ($this->sigPieces as $sigName => $sigPiece) {
            $sigPiece->_install($model);
        }
    }

    /**
     * @param string $paramName
     * @param float|string $value
     */
    protected function setFloatParam(string $paramName, $value)
    {
        $this->checkSetOperation($paramName, 'float');

        if (\is_numeric($value)) {
            $this->params[$paramName] = (float)$value;
        } elseif (\is_string($paramName)) {
            $this->params[$paramName] = $value;
            $this->resolvedDependencies = false;
        } else {
            throw new \InvalidArgumentException('Only numbers and string references are accepted');
        }
    }

    /**
     * @param string $paramName
     * @param int|string $value
     */
    protected function setIntParam(string $paramName, $value)
    {
        $this->checkSetOperation($paramName, 'int');

        if (\is_numeric($value) && \round($value) == $value) {
            $this->params[$paramName] = (int)$value;
        } elseif (\is_string($paramName)) {
            $this->params[$paramName] = $value;
            $this->resolvedDependencies = false;
        } else {
            throw new \InvalidArgumentException('Only integers and string references are accepted');
        }
    }

    /**
     * @param string $paramName
     * @param number[] $value
     */
    protected function setArrayParam(string $paramName, array $value)
    {
        $this->params[$paramName] = [];

        foreach ($value as $k => $v) {
            if (\is_numeric($v)) {
                $this->params[$paramName][$k] = (float)$v;
            } elseif (\is_string($v)) {
                $this->params[$paramName][$k] = $v;
                $this->resolvedDependencies = false;
            } else {
                throw new \InvalidArgumentException('Only numbers and string references are accepted');
            }
        }
    }

    /**
     * @param string $sigName
     * @param $sigPiece
     */
    protected function setSignalPiece(string $sigName, $sigPiece)
    {
        if (!\in_array($sigName, static::$SIGNAL_PIECES)) {
            throw new \InvalidArgumentException('This signal does not have the specified piece');
        }

        if (isset($this->sigPieces[$sigName]) && !\is_string($this->sigPieces[$sigName])) {
            throw new \LogicException('Resolved pieces cannot be rewritten');
        }

        if ($sigPiece instanceof Signal) {
            $this->sigPieces[$sigName] = $sigPiece;
        } elseif (\is_string($sigPiece)) {
            $this->sigPieces[$sigName] = $sigPiece;
            $this->resolvedDependencies = false;
        } else {
            throw new \InvalidArgumentException('Only Signal instances and string references are accepted');
        }
    }

    /**
     * @param Context $ctx
     * @return float
     */
    protected abstract function _at(Context $ctx) : float;

    /**
     * @throws UnresolvedReferenceException
     */
    private function throwUnresolvedReference()
    {
        $unresolvedReferences = [];

        foreach ($this->params as $paramName => $paramValue) {
            if (\is_string($paramValue)) {
                $unresolvedReferences[$paramName] = $paramValue;
            }
        }
        foreach ($this->sigPieces as $sigName => $signal) {
            if (\is_string($signal)) {
                $unresolvedReferences[$sigName] = $signal;
            }
        }

        throw new UnresolvedReferenceException($unresolvedReferences);
    }

    /**
     * @param string $paramName
     * @param string $paramType
     */
    private function checkSetOperation(string $paramName, string $paramType)
    {
        if (!\in_array($paramName, static::$SIGNAL_PARAMS)) {
            throw new \InvalidArgumentException('This signal does not have the specified parameter');
        }

        if (isset($this->params[$paramName]) && !\is_string($this->params[$paramName])) {
            throw new \LogicException('Resolved parameters cannot be rewritten');
        }

        if (isset($this->paramTypes[$paramName])) {
            if ($paramType !== $this->paramTypes[$paramName]) {
                throw new \InvalidArgumentException("Incorrect param type ($paramName), expected $paramType");
            }
        } else {
            $this->paramTypes[$paramName] = $paramType;
        }
    }

    /**
     * @param ModelBuilder $mb
     */
    private function resolveParamDependencies(ModelBuilder $mb)
    {
        foreach ($this->params as $paramName => $paramValue) {
            if (\is_string($paramValue)) {
                $v = $mb->getParam($paramValue);

                if (\is_int($v)) {
                    $this->setIntParam($paramName, $v);
                } elseif (\is_float($v)) {
                    $this->setFloatParam($paramName, $v);
                } else {
                    throw new \LogicException('There is some bug inside ModelBuilder (params resolution)');
                }
            } elseif (\is_array($paramValue)) {
                foreach ($paramValue as $key => $value) {
                    if (\is_string($value)) {
                        $v = $mb->getParam($value);

                        if (\is_int($v) || \is_float($v)) {
                            $this->params[$paramName][$key] = (float)$v;
                        } else {
                            throw new \LogicException('There is some bug inside ModelBuilder (params resolution)');
                        }
                    }
                }
            }
        }
    }

    /**
     * @param ModelBuilder $mb
     */
    private function resolvePiecesDependencies(ModelBuilder $mb)
    {
        foreach ($this->sigPieces as $sigName => $sigPiece) {
            if (\is_string($sigPiece)) {
                $this->sigPieces[$sigName] = $mb->getSignal($sigPiece);
            }
            if (\is_string($this->sigPieces[$sigName])) {
                throw new \LogicException('There is some bug inside ModelBuilder (signals resolution)');
            }
        }
    }
}
