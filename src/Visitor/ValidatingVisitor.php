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

namespace byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\OpeningNode;
use byrokrat\autogiro\Tree\MandateResponseNode;
use byrokrat\banking\Bankgiro;

class ValidatingVisitor extends AbstractVisitor
{
    /**
     * @var string[] List of messages describing found errors
     */
    private $errors = [];

    /**
     * @var Bankgiro
     */
    private $currentBankgiro;

    /**
     * Validate layout content
     */
    public function visitLayoutNode(LayoutNode $node)
    {
        if ($node->getOpeningNode()->getDate() != $node->getClosingNode()->getDate()) {
            $this->addError(
                "Non-matching dates in opening and closing nodes (opening: %s, closing: %s) on line %s",
                $node->getOpeningNode()->getDate()->format('Y-m-d'),
                $node->getClosingNode()->getDate()->format('Y-m-d'),
                (string)$node->getClosingNode()->getLineNr()
            );
        }

        if ($node->getClosingNode()->getNumberOfRecords() != count($node->getContentNodes())) {
            $this->addError(
                "Wrong record count in closing record (found: %s, expecting: %s) on line %s",
                (string)$node->getClosingNode()->getNumberOfRecords(),
                (string)count($node->getContentNodes()),
                (string)$node->getClosingNode()->getLineNr()
            );
        }
    }

    /**
     * Collect the current valid bankgiro number
     */
    public function visitOpeningNode(OpeningNode $node)
    {
        $this->currentBankgiro = $node->getBankgiro();
    }

    /**
     * Validate that bankgiro equals opening record bankgiro
     */
    public function visitMandateResponseNode(MandateResponseNode $node)
    {
        if ($node->getBankgiro() != $this->currentBankgiro) {
            $this->addError(
                "Non-matching bankgiro numbers in opening and mandate records (opening: %s, mandate: %s) on line %s",
                (string)$this->currentBankgiro,
                (string)$node->getBankgiro(),
                (string)$node->getLineNr()
            );
        }
    }

    /**
     * Check if any errors have been found
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Get list of messages describing found errors
     *
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Reset internal state
     *
     * @return void
     */
    public function reset()
    {
        $this->errors = [];
        unset($this->currentBankgiro);
    }

    /**
     * Add error message to store
     */
    private function addError(string $msg, string ...$args)
    {
        $this->errors[] = sprintf($msg, ...$args);
    }
}
