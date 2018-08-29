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

namespace byrokrat\autogiro\Writer;

use byrokrat\autogiro\Visitor\Visitor;
use byrokrat\autogiro\Exception\RuntimeException;
use byrokrat\autogiro\Exception\LogicException;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\Text;
use byrokrat\autogiro\Tree\Number;
use byrokrat\autogiro\Tree\Interval;
use byrokrat\amount\Currency\SEK;
use byrokrat\banking\AccountNumber;
use byrokrat\id\IdInterface;
use byrokrat\id\PersonalId;
use byrokrat\id\OrganizationId;

/**
 * Visitor that generates files to bgc from parse trees
 */
class PrintingVisitor extends Visitor
{
    /**
     * End-of-line chars used when generating files
     */
    const EOL = "\r\n";

    /**
     * @var Output
     */
    private $output;

    public function setOutput(Output $output): void
    {
        $this->output = $output;
    }

    public function beforeDate(Node $node): void
    {
        if ($node->getValue() instanceof \DateTimeInterface) {
            $this->output->write($node->getValue()->format('Ymd'));
        }
    }

    public function beforeImmediateDate(): void
    {
        $this->output->write('GENAST  ');
    }

    public function beforeText(Text $node): void
    {
        $this->output->write($node->getValue());
    }

    public function beforePayerNumber(Number $node): void
    {
        $this->output->write(str_pad($node->getValue(), 16, '0', STR_PAD_LEFT));
    }

    public function beforePayeeBgcNumber(Number $node): void
    {
        $this->output->write(str_pad($node->getValue(), 6, '0', STR_PAD_LEFT));
    }

    public function beforePayeeBankgiro(Node $node): void
    {
        if ($node->getValue() instanceof AccountNumber) {
            $account = $node->getValue();
            $this->output->write(
                str_pad($account->getSerialNumber() . $account->getCheckDigit(), 10, '0', STR_PAD_LEFT)
            );
        }
    }

    public function beforeAccount(Node $node): void
    {
        if ($node->getValue() instanceof AccountNumber) {
            $account = $node->getValue();
            $this->output->write(
                $account->getClearingNumber()
                . str_pad($account->getSerialNumber() . $account->getCheckDigit(), 12, '0', STR_PAD_LEFT)
            );
        }
    }

    public function beforeInterval(Interval $node): void
    {
        $this->output->write($node->getValue());
    }

    public function beforeAmount(Node $node): void
    {
        if ($node->getValue() instanceof SEK) {
            $amount = $node->getValue();

            if ($amount->isGreaterThan(new SEK('9999999999.99')) || $amount->isLessThan(new SEK('-9999999999.99'))) {
                throw new RuntimeException('Amount must be between 9999999999.99 and -9999999999.99');
            }

            $this->output->write(
                str_pad($amount->getSignalString(), 12, '0', STR_PAD_LEFT)
            );
        }
    }

    public function beforeStateId(Node $node): void
    {
        if ($node->getValue() instanceof PersonalId) {
            $this->output->write($node->getValue()->format('Ymdsk'));
        }
        if ($node->getValue() instanceof OrganizationId) {
            $this->output->write($node->getValue()->format('00Ssk'));
        }
    }

    private function assertAttribute(Node $node, string $attr, string $classname): void
    {
        if (!$node->hasAttribute($attr) || !$node->getAttribute($attr) instanceof $classname) {
            throw new LogicException("Failing attribute '$attr' in {$node->getName()}");
        }
    }

    public function beforeOpening(): void
    {
        $this->output->write('01');
    }

    public function afterOpening(): void
    {
        $this->output->write(self::EOL);
    }

    public function beforeCreateMandateRequest(): void
    {
        $this->output->write('04');
    }

    public function afterCreateMandateRequest(): void
    {
        $this->output->write(self::EOL);
    }

    public function beforeDeleteMandateRequest(): void
    {
        $this->output->write('03');
    }

    public function afterDeleteMandateRequest(): void
    {
        $this->output->write(self::EOL);
    }

    public function beforeAcceptDigitalMandateRequest(): void
    {
        $this->output->write('04');
    }

    public function afterAcceptDigitalMandateRequest(): void
    {
        $this->output->write(self::EOL);
    }

    public function beforeRejectDigitalMandateRequest(): void
    {
        $this->output->write('04');
    }

    public function afterRejectDigitalMandateRequest(): void
    {
        $this->output->write(self::EOL);
    }

    public function beforeUpdateMandateRequest(): void
    {
        $this->output->write('05');
    }

    public function afterUpdateMandateRequest(): void
    {
        $this->output->write(self::EOL);
    }

    public function beforeIncomingPaymentRequest(): void
    {
        $this->output->write('82');
    }

    public function afterIncomingPaymentRequest(): void
    {
        $this->output->write(self::EOL);
    }

    public function beforeOutgoingPaymentRequest(): void
    {
        $this->output->write('32');
    }

    public function afterOutgoingPaymentRequest(): void
    {
        $this->output->write(self::EOL);
    }
}
