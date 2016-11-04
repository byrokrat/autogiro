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
     * @var Processor[]
     */
    private $processors;

    /**
     * @param Grammar     $grammar
     * @param Processor[] $processors
     */
    public function __construct(Grammar $grammar = null, array $processors = [])
    {
        $this->grammar = $grammar ?: new Grammar;
        $this->processors = $processors;
    }

    /**
     * @throws ParserException If parsning fails
     */
    public function parse(string $content): FileNode
    {
        try {
            $node = $this->grammar->parse($content);
        } catch (\Exception $exception) {
            throw new ParserException([$exception->getMessage()]);
        }

        $errors = [];

        foreach ($this->processors as $processor) {
            $processor->resetErrors();
            $node->accept($processor);
            $errors = array_merge($errors, $processor->getErrors());
        }

        if (!empty($errors)) {
            throw new ParserException($errors);
        }

        return $node;
    }
}
