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

namespace byrokrat\autogiro\Tree;

/**
 * A record maps to a line in an autogiro files
 *
 * @see \byrokrat\autogiro\Tree\Request for the set of request records
 * @see \byrokrat\autogiro\Tree\Response for the set of response records
 */
class RecordNode extends Node
{
    use TypeTrait;

    /**
     * @param integer $lineNr Line number of record definition
     * @param Node[]  $nodes  The nodes composing record
     */
    public function __construct(int $lineNr, array $nodes)
    {
        parent::__construct($lineNr);

        foreach ($nodes as $name => $node) {
            $this->addChild((string)$name, $node);
        }
    }
}
