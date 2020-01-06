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

declare(strict_types = 1);

namespace byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Tree\Node;
use Money\Money;

final class SummaryVisitor extends Visitor
{
    use ErrorAwareTrait;

    /**
     * @var Money[]
     */
    private $summaries = [];

    public function beforeAutogiroFile(): void
    {
        $this->summaries = [];
    }

    public function afterRecord(Node $node): void
    {
        if ($amount = $node->getChild('Amount')->getValueFrom('Object')) {
            $summary = $this->summaries[$node->getName()] ?? $amount->subtract($amount);
            $this->summaries[$node->getName()] = $summary->add($amount);
        }
    }

    public function afterSummary(Node $node): void
    {
        $expectedAmount = $node->getChild('Amount')->getValueFrom('Object');

        if (!$expectedAmount) {
            return;
        }

        $currentAmount = $this->summaries[(string)$node->getValueFrom('Text')]
            ?? $expectedAmount->subtract($expectedAmount);

        if (!$expectedAmount->absolute()->equals($currentAmount->absolute())) {
            $this->getErrorObject()->addError(
                "Invalid %s node summary (found: %s, expected: %s) on line %s",
                (string)$node->getValueFrom('Text'),
                (string)$currentAmount->getAmount(),
                (string)$expectedAmount->getAmount(),
                (string)$node->getLineNr()
            );
        }
    }
}
