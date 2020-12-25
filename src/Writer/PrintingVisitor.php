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

namespace byrokrat\autogiro\Writer;

use byrokrat\autogiro\Visitor\Visitor;
use byrokrat\autogiro\Exception\RuntimeException;
use byrokrat\autogiro\Tree\Node;
use byrokrat\banking\AccountNumber;
use byrokrat\id\IdInterface;
use byrokrat\id\PersonalId;
use byrokrat\id\OrganizationId;
use Money\Money;
use Money\MoneyFormatter;

/**
 * Visitor that generates files to bgc from parse trees
 */
class PrintingVisitor extends Visitor
{
    public const EOL = "\r\n";

    /**
     * @var ?Output
     */
    private $output;

    /**
     * @var MoneyFormatter
     */
    private $moneyFormatter;

    public function __construct(MoneyFormatter $moneyFormatter)
    {
        $this->moneyFormatter = $moneyFormatter;
    }

    public function setOutput(Output $output): void
    {
        $this->output = $output;
    }

    private function write(string $text): void
    {
        if ($this->output) {
            $this->output->write($text);
        }
    }

    public function beforeDate(Node $node): void
    {
        if ($node->getValue() instanceof \DateTimeInterface) {
            $this->write($node->getValue()->format('Ymd'));
        }
    }

    public function beforeImmediateDate(): void
    {
        $this->write('GENAST  ');
    }

    public function beforeText(Node $node): void
    {
        $this->write($node->getValue());
    }

    public function beforePayerNumber(Node $node): void
    {
        $this->write(str_pad($node->getValue(), 16, '0', STR_PAD_LEFT));
    }

    public function beforePayeeBgcNumber(Node $node): void
    {
        $this->write(str_pad($node->getValue(), 6, '0', STR_PAD_LEFT));
    }

    public function beforePayeeBankgiro(Node $node): void
    {
        if ($node->getValue() instanceof AccountNumber) {
            $account = $node->getValue();
            $this->write(
                str_pad($account->getSerialNumber() . $account->getCheckDigit(), 10, '0', STR_PAD_LEFT)
            );
        }
    }

    public function beforeAccount(Node $node): void
    {
        if ($node->getValue() instanceof AccountNumber) {
            $account = $node->getValue();
            $this->write(
                $account->getClearingNumber()
                . str_pad($account->getSerialNumber() . $account->getCheckDigit(), 12, '0', STR_PAD_LEFT)
            );
        }
    }

    public function beforeInterval(Node $node): void
    {
        if (!in_array((int)$node->getValue(), range('0', '8'))) {
            throw new RuntimeException('Interval must be between 0 and 8');
        }

        $this->write($node->getValue());
    }

    public function beforeRepetitions(Node $node): void
    {
        if (!ctype_digit($node->getValue()) || strlen($node->getValue()) > 3) {
            throw new RuntimeException("Invalid number of repitions: {$node->getValue()}");
        }

        $this->write(
            $node->getValue() ? str_pad($node->getValue(), 3, '0', STR_PAD_LEFT) : '   '
        );
    }

    public function beforeAmount(Node $node): void
    {
        $amount = $node->getValue();

        if ($amount instanceof Money) {
            if ($amount->getCurrency()->getCode() != 'SEK') {
                throw new RuntimeException('Printing visitor can only work with SEK');
            }

            if ($amount->greaterThan(Money::SEK('999999999999')) || $amount->lessThan(Money::SEK('-999999999999'))) {
                throw new RuntimeException('Amount must be between 9999999999.99 and -9999999999.99');
            }

            $this->write(
                str_pad($this->moneyFormatter->format($amount), 12, '0', STR_PAD_LEFT)
            );
        }
    }

    public function beforeStateId(Node $node): void
    {
        if ($node->getValue() instanceof PersonalId) {
            $this->write($node->getValue()->format('Ymdsk'));
        }
        if ($node->getValue() instanceof OrganizationId) {
            $this->write($node->getValue()->format('00Ssk'));
        }
    }

    public function beforeOpening(): void
    {
        $this->write('01');
    }

    public function beforeCreateMandateRequest(): void
    {
        $this->write('04');
    }

    public function beforeDeleteMandateRequest(): void
    {
        $this->write('03');
    }

    public function beforeAcceptDigitalMandateRequest(): void
    {
        $this->write('04');
    }

    public function beforeRejectDigitalMandateRequest(): void
    {
        $this->write('04');
    }

    public function beforeUpdateMandateRequest(): void
    {
        $this->write('05');
    }

    public function beforeIncomingPaymentRequest(): void
    {
        $this->write('82');
    }

    public function beforeOutgoingPaymentRequest(): void
    {
        $this->write('32');
    }

    public function beforeAmendmentRequest(): void
    {
        $this->write('23');
    }

    public function afterRecord(): void
    {
        $this->write(self::EOL);
    }
}
