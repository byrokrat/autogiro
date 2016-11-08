<?php
/**
 * This file is part of byrokrat\autogiro.
 *
 * byrokrat\autogiro is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * byrokrat\autogiro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with byrokrat\autogiro. If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2016 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro;

use byrokrat\autogiro\Processor\Processor;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Exception\ParserException;

/**
 * Facade to Grammar with error handling
 */
class Parser
{
    /**
     * @var Grammar
     */
    private $grammar;

    /**
     * @var Processor
     */
    private $processor;

    public function __construct(Grammar $grammar, Processor $processor)
    {
        $this->grammar = $grammar;
        $this->processor = $processor;
    }

    /**
     * @throws ParserException If parsning fails
     */
    public function parse(string $content): FileNode
    {
        try {
            $fileNode = $this->grammar->parse($content);
            $fileNode->accept($this->processor);
        } catch (\Exception $exception) {
            throw new ParserException([$exception->getMessage()]);
        }

        if ($this->processor->hasErrors()) {
            throw new ParserException($this->processor->getErrors());
        }

        return $fileNode;
    }
}
