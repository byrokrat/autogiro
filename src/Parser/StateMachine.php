<?php
/**
 * This file is part of byrokrat/autogiro.
 *
 * byrokrat/autogiro is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * byrokrat/autogiro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with byrokrat/autogiro. If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2016 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro\Parser;

use byrokrat\autogiro\Exception\InvalidStateException;

/**
 * Handle states and state transitions
 */
class StateMachine
{
    /**
     * Default init state
     */
    const STATE_INIT = 'state_init';

    /**
     * Done state
     */
    const STATE_DONE = 'state_done';

    /**
     * @var string The current state
     */
    private $currentState;

    /**
     * @var array Map of valid transitions
     */
    private $validTransitions;

    /**
     * Load initial state and define valid transitions
     *
     * Transitions are defined in an array of arrays where pre transition states
     * are keys in the outer array and the values of the inner arrays represent
     * valid post transitions states.
     *
     * @param string $initialState
     * @param array  $validTransitions
     */
    public function __construct(array $validTransitions, string $initialState = self::STATE_INIT)
    {
        $this->validTransitions = $validTransitions;
        $this->currentState = $initialState;
    }

    /**
     * Set machine in a new state
     *
     * @throws InvalidStateException If transition is not valid
     */
    public function transitionTo(string $newState)
    {
        if (!array_key_exists($this->currentState, $this->validTransitions)) {
            throw new InvalidStateException("Unexpected transaction code $newState (expecting END)");
        }

        if (!in_array($newState, $this->validTransitions[$this->currentState])) {
            throw new InvalidStateException(
                sprintf(
                    'Unexpected transaction code %s (expecting one of [%s])',
                    $newState,
                    implode($this->validTransitions[$this->currentState], ', ')
                )
            );
        }

        $this->currentState = $newState;
    }

    /**
     * Get current state
     */
    public function getState(): string
    {
        return $this->currentState;
    }
}
