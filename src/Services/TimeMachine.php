<?php

namespace GiacomoFurlan\TimeMachine\Services;

use DateTime;
use GiacomoFurlan\TimeMachine\Interfaces\FactoryInterface;
use GiacomoFurlan\TimeMachine\Interfaces\TimeMachineInterface;

/**
 * Class TimeMachine
 * Default implementation of TimeMachineInterface
 * @package GiacomoFurlan\TimeMachine\Services
 */
class TimeMachine implements TimeMachineInterface
{
    /**
     * Calculate the production of the factory from the last time it's being calculated till $till
     * @param FactoryInterface $factory
     * @param DateTime $till
     * @return float
     */
    public function calculateFactoryProduction(FactoryInterface $factory, DateTime $till)
    {
        $nextLevel = $factory->getNextLevelUpgradeFinish();
        $lastCalculation = $factory->getLastTimeCalculated();
        $lastCalculationTimestamp = $lastCalculation->getTimestamp();
        $productionPerSecond = $factory->getHourlyProduction($factory->getLevel()) / 60 / 60;

        $addend = 0;
        // There is an ongoing factory upgrade and it's between the last calculation and the end of the calculation
        if ($nextLevel && $nextLevel > $lastCalculation && $nextLevel < $till) {
            $nextLevelTimestamp = $nextLevel->getTimestamp();
            $secondsToCalculate = $nextLevelTimestamp - $lastCalculationTimestamp;
            $addend = $productionPerSecond * $secondsToCalculate;

            $lastCalculationTimestamp = $nextLevelTimestamp;
            $productionPerSecond = $factory->getHourlyProduction($factory->getLevel() + 1) / 60 / 60;
        }

        $secondsToCalculate = $till->getTimestamp() - $lastCalculationTimestamp;

        return $addend + $productionPerSecond * $secondsToCalculate;
    }
}
