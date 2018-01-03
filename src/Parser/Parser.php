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
 * Copyright 2016-18 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro\Parser;

use byrokrat\autogiro\Visitor\Visitor;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Exception\ContentException;

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
     * @var Visitor
     */
    private $visitor;

    public function __construct(Grammar $grammar, Visitor $visitor)
    {
        $this->grammar = $grammar;
        $this->visitor = $visitor;
    }

    /**
     * @throws ContentException If grammar fails
     */
    public function parse(string $content): FileNode
    {
        try {
            $tree = $this->grammar->parse($content);
        } catch (\InvalidArgumentException $exception) {
            throw new ContentException(["Parser: {$exception->getMessage()}"]);
        }

        $tree->accept($this->visitor);
        return $tree;
    }
}
