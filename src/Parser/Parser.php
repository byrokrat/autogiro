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

use byrokrat\autogiro\Section;
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

    /**
     * Set parsing strategy
     */
    public function __construct(Strategy\Strategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * Parse file
     */
    public function parse(\SplFileObject $file): Section
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

                if (!method_exists(...$handler) || !is_callable($handler)) {
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
