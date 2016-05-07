<?php

declare(strict_types = 1);

namespace byrokrat\autogiro\Parser;

class StateMachineTest extends \PHPUnit_Framework_TestCase
{
    public function testNoRegisteredTransition()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\InvalidStateException');
        (new StateMachine([], 'A'))->transitionTo('B');
    }

    public function testInvalidTransition()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\InvalidStateException');
        (new StateMachine(['A' => ['B', 'C']], 'A'))->transitionTo('D');
    }

    public function testValidTransition()
    {
        $stateMachine = new StateMachine(
            [
                'A' => ['B'],
                'B' => ['C']
            ],
            'A'
        );
        $stateMachine->transitionTo('B');
        $stateMachine->transitionTo('C');
        $this->assertSame('C', $stateMachine->getState());
    }

    public function testDefaultInitState()
    {
        $this->assertSame(
            StateMachine::STATE_INIT,
            (new StateMachine([]))->getState()
        );
    }
}
