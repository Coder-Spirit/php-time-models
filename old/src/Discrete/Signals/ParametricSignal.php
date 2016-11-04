<?php
declare(strict_types=1);


namespace Litipk\TimeModels\Discrete\Signals;


use Litipk\TimeModels\Discrete\Model;


interface ParametricSignal
{
    public function withParametersFromModel(Model $model) : Signal;
}
