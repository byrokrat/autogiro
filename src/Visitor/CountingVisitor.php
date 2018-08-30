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

namespace byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Tree\Node;

class CountingVisitor extends ErrorAwareVisitor
{
    /**
     * @var int[]
     */
    private $counts = [];

    public function beforeAutogiroFile(): void
    {
        $this->counts = [];
    }

    public function beforeSection(Node $node): void
    {
        $this->increment($node->getName());
    }

    public function beforeRecord(Node $node): void
    {
        $this->increment($node->getName());
    }

    public function beforeCount(Node $node): void
    {
        $expectedCount = (int)$node->getChild('Number')->getValue();
        $currentCount = $this->getCount((string)$node->getChild('Text')->getValue());

        if ($expectedCount != $currentCount) {
            $this->getErrorObject()->addError(
                "Invalid nr of %s nodes (found: %s, expected: %s) on line %s",
                (string)$node->getChild('Text')->getValue(),
                (string)$currentCount,
                (string)$expectedCount,
                (string)$node->getLineNr()
            );
        }
    }

    private function getCount(string $nodeName): int
    {
        return $this->counts[$nodeName] ?? 0;
    }

    private function increment(string $nodeName): void
    {
        $this->counts[$nodeName] = $this->getCount($nodeName) + 1;
    }
}
