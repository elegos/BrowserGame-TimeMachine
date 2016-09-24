<?php

namespace GiacomoFurlan\TimeMachine\Interfaces;

use DateTime;

/**
 * Interface FactoryInterface
 * Factory as game factory, used to produce game resources
 *
 * @package GiacomoFurlan\TimeMachine\Interfaces
 */
interface FactoryInterface
{
    /**
     * Get the actual factory's production level
     * @return int
     */
    public function getLevel() : int;

    /**
     * Get the hourly production rate
     * @param int $level
     * @return float
     */
    public function getHourlyProduction($level) : float;

    /**
     * Get the date of when the factory will upgrade to the next production level
     * @return DateTime|null
     */
    public function getNextLevelUpgradeFinish();

    /**
     * Get the last time the production of this factory was calculated
     * @return DateTime
     */
    public function getLastTimeCalculated() : DateTime;
}
