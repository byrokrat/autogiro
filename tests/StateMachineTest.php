<?php

declare(strict_types=1);

namespace byrokrat\autogiro;

class StateMachineTest extends \PHPUnit_Framework_TestCase
{
    public function testNoRegisteredTransition()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\InvalidStateException');
        (new StateMachine('A', []))->transitionTo('B');
    }

    public function testInvalidTransition()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\InvalidStateException');
        (new StateMachine('A', ['A' => ['B', 'C']]))->transitionTo('D');
    }

    public function testValidTransition()
    {
        $stateMachine = new StateMachine(
            'A',
            [
                'A' => ['B'],
                'B' => ['C']
            ]
        );
        $stateMachine->transitionTo('B');
        $stateMachine->transitionTo('C');
        $this->assertSame('C', $stateMachine->getState());
    }
}
