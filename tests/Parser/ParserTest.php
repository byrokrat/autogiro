<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Parser;

use byrokrat\autogiro\Line;
use byrokrat\autogiro\Section;

/**
 * @covers byrokrat\autogiro\Parser\Parser
 */
class ParserTest extends \byrokrat\autogiro\BaseTestCase
{
    public function testExceptionOnMissingHandler()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\InvalidFileException');

        $states = $this->prophesize(StateMachine::CLASS);
        $states->getState()->willReturn('00');
        $states->transitionTo('01')->shouldBeCalledTimes(1);
        $states->transitionTo('02');

        $strategy = $this->prophesize(Strategy\Strategy::CLASS);
        $strategy->createStates()->willReturn($states->reveal())->shouldBeCalledTimes(1);
        $strategy->begin()->shouldBeCalledTimes(1);

        $file = $this->createSplFileObject("01\n02\n");

        (new Parser($strategy->reveal()))->parse($file);
    }

    public function testParse()
    {
        $states = $this->prophesize(StateMachine::CLASS);
        $states->getState()->willReturn('01');
        $states->transitionTo('01')->shouldBeCalledTimes(1);
        $states->transitionTo(StateMachine::STATE_DONE)->shouldBeCalledTimes(1);

        $section = $this->prophesize(Section::CLASS);

        $strategy = new class($states->reveal(), $section->reveal()) implements Strategy\Strategy {
            public function __construct($states, $section)
            {
                $this->states = $states;
                $this->section = $section;
            }

            public function createStates(): StateMachine
            {
                return $this->states;
            }

            public function begin()
            {
            }

            public function done(): Section
            {
                return $this->section;
            }

            public function on01(Line $line)
            {
            }
        };

        $file = $this->createSplFileObject("01\n");

        $this->assertSame(
            $section->reveal(),
            (new Parser($strategy))->parse($file)
        );
    }
}
