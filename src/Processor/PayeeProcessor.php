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

namespace byrokrat\autogiro\Processor;

use byrokrat\autogiro\Tree\OpeningNode;
use byrokrat\autogiro\Tree\FileNode;

/**
 * Validate that payee bankgiro and BGC customer number are constant within tree
 */
class PayeeProcessor extends Processor
{
    /**
     * @var string
     */
    private $bankgiro;

    /**
     * @var string
     */
    private $custNr;

    /**
     * Reset bankgiro and customer number before a new file is traversed
     */
    public function beforeFileNode(FileNode $fileNode)
    {
        $this->bankgiro = '';
        $this->custNr = '';
    }

    /**
     * Validate bankgiro and customer number of section
     */
    public function beforeOpeningNode(OpeningNode $node)
    {
        if (!$this->bankgiro) {
            $this->bankgiro = $node->getChild('bankgiro')->getValue();
        }

        if (!$this->custNr) {
            $this->custNr = $node->getChild('customer_number')->getValue();
        }

        if ($node->getChild('bankgiro')->getValue() != $this->bankgiro) {
            $this->addError(
                "Non-matching payee bankgiro numbers within file (%s and %s) on line %s",
                $this->bankgiro,
                $node->getChild('bankgiro')->getValue(),
                (string)$node->getLineNr()
            );
        }

        if ($node->getChild('customer_number')->getValue() != $this->custNr) {
            $this->addError(
                "Non-matching payee BGC customer numbers within file (%s and %s) on line %s",
                $this->custNr,
                $node->getChild('customer_number')->getValue(),
                (string)$node->getLineNr()
            );
        }
    }
}
