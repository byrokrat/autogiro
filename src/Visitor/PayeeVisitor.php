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

use byrokrat\autogiro\Tree\PayeeBankgiroNode;
use byrokrat\autogiro\Tree\PayeeBgcNumberNode;

/**
 * Validate that payee bankgiro and BGC customer number are constant within tree
 */
class PayeeVisitor extends ErrorAwareVisitor
{
    /**
     * @var string Payee bankgiro account number
     */
    private $payeeBg;

    /**
     * @var string Payee BGC customer number
     */
    private $payeeBgcNr;

    /**
     * Reset payee bankgiro and customer number before a new file is traversed
     */
    public function beforeFileNode(): void
    {
        $this->payeeBg = '';
        $this->payeeBgcNr = '';
    }

    /**
     * Validate payee bankgiro number
     */
    public function beforePayeeBankgiroNode(PayeeBankgiroNode $node): void
    {
        if (!$this->payeeBg) {
            $this->payeeBg = $node->getValue();
        }

        if ($node->getValue() != $this->payeeBg) {
            $this->getErrorObject()->addError(
                "Non-matching payee bankgiro numbers (expecting: %s, found: %s) on line %s",
                $this->payeeBg,
                $node->getValue(),
                (string)$node->getLineNr()
            );
        }
    }

    /**
     * Validate payee BGC customer number
     */
    public function beforePayeeBgcNumberNode(PayeeBgcNumberNode $node): void
    {
        if (!$this->payeeBgcNr) {
            $this->payeeBgcNr = $node->getValue();
        }

        if ($node->getValue() != $this->payeeBgcNr) {
            $this->getErrorObject()->addError(
                "Non-matching payee BGC customer numbers (expecting: %s, found: %s) on line %s",
                $this->payeeBgcNr,
                $node->getValue(),
                (string)$node->getLineNr()
            );
        }
    }
}
