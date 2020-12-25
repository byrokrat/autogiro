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
 * Copyright 2016-20 Hannes Forsgård
 */

declare(strict_types=1);

namespace byrokrat\autogiro\Parser;

use byrokrat\autogiro\Visitor\VisitorInterface;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Exception\ParserException;
use ForceUTF8\Encoding;

final class Parser implements ParserInterface
{
    /**
     * @var Grammar
     */
    private $grammar;

    /**
     * @var VisitorInterface
     */
    private $visitor;

    public function __construct(Grammar $grammar, VisitorInterface $visitor)
    {
        $this->grammar = $grammar;
        $this->visitor = $visitor;
    }

    public function parse(string $content): Node
    {
        try {
            $tree = $this->grammar->parse(
                Encoding::toUTF8($content)
            );
        } catch (\InvalidArgumentException $exception) {
            throw new ParserException("Parser: {$exception->getMessage()}");
        }

        $tree->accept($this->visitor);

        return $tree;
    }
}
