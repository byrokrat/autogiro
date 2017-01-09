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

namespace byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Record\RecordNode;

/**
 * Generic record container node
 */
class LayoutNode extends Node
{
    public function __construct(RecordNode ...$nodes)
    {
        foreach ($nodes as $key => $node) {
            $this->setChild((string)($key + 1), $node);
        }

        if (isset($nodes[0]) && $nodes[0]->hasChild('layout_name')) {
            $this->setAttribute('layout_name', $nodes[0]->getChild('layout_name')->getValue());
        }
    }

    public function getLineNr(): int
    {
        return $this->getChild('1')->getLineNr();
    }
}
