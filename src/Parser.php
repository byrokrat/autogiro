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

use byrokrat\autogiro\Visitor\ValidatingVisitor;
use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Exception\ParserException;

class Parser
{
    /**
     * @var Grammar
     */
    private $grammar;

    /**
     * @var ValidatingVisitor
     */
    private $validator;

    public function __construct(Grammar $grammar, ValidatingVisitor $validator)
    {
        $this->grammar = $grammar;
        $this->validator = $validator;
    }

    /**
     * @throws ParserException If parsning fails
     */
    public function parse(string $content): LayoutNode
    {
        $this->grammar->resetLineCount();

        try {
            $node = $this->grammar->parse($content);
        } catch (\InvalidArgumentException $exception) {
            throw new ParserException([$exception->getMessage()]);
        } catch (\Exception $exception) {
            throw new ParserException([$exception->getMessage() . " on line {$this->grammar->getCurrentLineCount()}"]);
        }

        $this->validator->reset();
        $node->accept($this->validator);

        if ($this->validator->hasErrors()) {
            throw new ParserException($this->validator->getErrors());
        }

        return $node;
    }
}
