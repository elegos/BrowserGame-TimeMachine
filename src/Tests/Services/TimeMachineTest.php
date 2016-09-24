<?php

namespace GiacomoFurlan\TimeMachine\Tests\Services;

use DateTime;
use GiacomoFurlan\TimeMachine\Interfaces\FactoryInterface;
use GiacomoFurlan\TimeMachine\Services\TimeMachine;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class TimeMachineTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider calculateFactoryProductionDataProvider()
     * @param DateTime      $till
     * @param DateTime|null $lastTimeCalculated
     * @param DateTime|null $nextLevelUpgrade
     * @param int           $level
     * @param float         $hourlyProductionForCurrentLevel
     * @param float         $hourlyProductionForNextLevel
     * @param float         $expected
     * @param string        $reason
     */
    public function testCalculateFactoryProduction(
        DateTime $till,
        DateTime $lastTimeCalculated = null,
        DateTime $nextLevelUpgrade = null,
        $level,
        $hourlyProductionForCurrentLevel,
        $hourlyProductionForNextLevel,
        $expected,
        $reason
    ) {
        /** @var FactoryInterface|ObjectProphecy $factory */
        $factory = $this->prophesize(FactoryInterface::class);
        $factory->getLevel()->willReturn($level);
        $factory->getHourlyProduction(Argument::exact($level))->willReturn($hourlyProductionForCurrentLevel);
        $factory->getHourlyProduction(Argument::exact($level + 1))->willReturn($hourlyProductionForNextLevel);
        $factory->getLastTimeCalculated()->willReturn($lastTimeCalculated);
        $factory->getNextLevelUpgradeFinish()->willReturn($nextLevelUpgrade);

        $timeMachine = new TimeMachine();
        static::assertEquals($expected, $timeMachine->calculateFactoryProduction($factory->reveal(), $till), $reason);
    }

    public function calculateFactoryProductionDataProvider()
    {
        $now = new DateTime('now');

        $lastTimeCalculated = clone $now;
        $lastTimeCalculated->modify('-1 hour');

        $nextLevel = clone $now;
        $nextLevel->modify('-30 minutes');

        $nextLevelBeforeStart = clone $lastTimeCalculated;
        $nextLevelBeforeStart->modify('-1 day');

        $nextLevelAfterEnd = clone $now;

        return [
            [$now, $lastTimeCalculated, null, 1, 60, 0, 60, 'Given hourly production of 60 and one hour, expected production of 60'],
            [$now, $lastTimeCalculated, $nextLevel, 1, 60, 80, 70, 'Given hourly production of 60 for 30 minutes and 80 for 30 minutes, expected production of 70'],
            [$now, $lastTimeCalculated, $nextLevelBeforeStart, 1, 60, 80, 60, 'Given hourly production of 60 and one hour, next level before calculation start, expected production of 60'],
            [$now, $lastTimeCalculated, $nextLevelAfterEnd, 1, 60, 80, 60, 'Given hourly production of 60 and one hour, next level after (on) calculation end, expected production of 60'],
        ];
    }
}
