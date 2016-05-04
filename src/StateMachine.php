<?php

declare(strict_types=1);

namespace byrokrat\autogiro;

use byrokrat\autogiro\Exception\InvalidStateException;

/**
 * Handle states and state transitions
 */
class StateMachine
{
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
    public function __construct(string $initialState, array $validTransitions)
    {
        $this->currentState = $initialState;
        $this->validTransitions = $validTransitions;
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
