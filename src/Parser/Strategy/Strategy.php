<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Parser\Strategy;

use byrokrat\autogiro\Parser\StateMachine;

/**
 * Definies a parsing strategy
 */
interface Strategy
{
    /**
     * Get FSM for valid transaction codes
     */
    public function createStates(): StateMachine;

    /**
     * Begin parsing new file
     */
    public function begin();

    /**
     * Get generated data when parsing is complete
     */
    public function done(): array;
}
