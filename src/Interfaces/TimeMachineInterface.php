<?php

namespace GiacomoFurlan\TimeMachine\Interfaces;

use DateTime;

interface TimeMachineInterface
{
    /**
     * Calculate the production of the factory from the last time it's being calculated till $till
     * @param FactoryInterface $factory
     * @param DateTime         $till
     * @return float
     */
    public function calculateFactoryProduction(FactoryInterface $factory, DateTime $till);
}
