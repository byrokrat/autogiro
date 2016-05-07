<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Parser;

use byrokrat\autogiro\Line;
use byrokrat\autogiro\Exception;

/**
 * Prase raw autogiro files
 */
class Parser
{
    /**
     * @var Strategy\Strategy
     */
    private $strategy;

    public function __construct(Strategy\Strategy $strategy)
    {
        $this->strategy = $strategy;
    }

    // TODO vilken typ av objekt ska parse returnera??
    public function parse(\SplFileObject $file)
    {
        $lineNumber = 'undefined';

        try {
            $states = $this->strategy->createStates();
            $this->strategy->begin();

            foreach ($file as $lineNumber => $line) {
                $line = new Line($line);

                if ($line->isEmpty()) {
                    continue;
                }

                $states->transitionTo($line->getTransactionCode());
                $handler = [$this->strategy, "on{$states->getState()}"];

                if (!is_callable($handler)) {
                    throw new Exception\LogicException("Missing handler for state {$states->getState()}");
                }

                $handler($line);
            }

            $states->transitionTo(StateMachine::STATE_DONE);
            return $this->strategy->done();

        } catch (\Exception $e) {
            throw new Exception\InvalidFileException(
                "{$e->getMessage()} on line $lineNumber in '{$file->getBasename()}'",
                0,
                $e
            );
        }
    }
}
