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
 * Copyright 2016-17 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro\Writer;

use byrokrat\autogiro\Visitor\Visitor;
use byrokrat\autogiro\Exception\LogicException;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\TextNode;
use byrokrat\autogiro\Tree\PayeeBgcNumberNode;
use byrokrat\autogiro\Tree\PayeeBankgiroNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\AccountNode;
use byrokrat\autogiro\Tree\AmountNode;
use byrokrat\autogiro\Tree\IntervalNode;
use byrokrat\autogiro\Tree\IdNode;
use byrokrat\autogiro\Tree\RepetitionsNode;
use byrokrat\amount\Currency\SEK;
use byrokrat\banking\AccountNumber;
use byrokrat\id\Id;
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

    public function setOutput(Output $output)
    {
        $this->output = $output;
    }

    public function beforeDateNode(DateNode $node)
    {
        $this->assertAttribute($node, 'date', \DateTimeInterface::CLASS);
        $this->output->write($node->getAttribute('date')->format('Ymd'));
    }

    public function beforeImmediateDateNode()
    {
        $this->output->write('GENAST  ');
    }

    public function beforeTextNode(TextNode $node)
    {
        $this->output->write($node->getValue());
    }

    public function beforePayeeBgcNumberNode(PayeeBgcNumberNode $node)
    {
        $this->output->write(str_pad($node->getValue(), 6, '0', STR_PAD_LEFT));
    }

    public function beforePayeeBankgiroNode(PayeeBankgiroNode $node)
    {
        $this->assertAttribute($node, 'account', AccountNumber::CLASS);
        $number = $node->getAttribute('account')->getSerialNumber() . $node->getAttribute('account')->getCheckDigit();
        $this->output->write(str_pad($number, 10, '0', STR_PAD_LEFT));
    }

    public function beforePayerNumberNode(PayerNumberNode $node)
    {
        $this->output->write(str_pad($node->getValue(), 16, '0', STR_PAD_LEFT));
    }

    public function beforeAccountNode(AccountNode $node)
    {
        $this->assertAttribute($node, 'account', AccountNumber::CLASS);
        $number = $node->getAttribute('account')->getSerialNumber() . $node->getAttribute('account')->getCheckDigit();
        $this->output->write(
            $node->getAttribute('account')->getClearingNumber()
            . str_pad($number, 12, '0', STR_PAD_LEFT)
        );
    }

    public function beforeIntervalNode(IntervalNode $node)
    {
        $this->output->write($node->getValue());
    }

    public function beforeRepetitionsNode(RepetitionsNode $node)
    {
        $this->output->write($node->getValue());
    }

    public function beforeAmountNode(AmountNode $node)
    {
        $this->assertAttribute($node, 'amount', SEK::CLASS);
        $this->output->write(
            str_pad($node->getAttribute('amount')->getSignalString(), 12, '0', STR_PAD_LEFT)
        );
    }

    public function beforeIdNode(IdNode $node)
    {
        $this->assertAttribute($node, 'id', Id::CLASS);
        if ($node->getAttribute('id') instanceof PersonalId) {
            $this->output->write($node->getAttribute('id')->format('Ymdsk'));
        }
        if ($node->getAttribute('id') instanceof OrganizationId) {
            $this->output->write($node->getAttribute('id')->format('00Ssk'));
        }
    }

    private function assertAttribute(Node $node, string $attr, string $classname)
    {
        if (!$node->hasAttribute($attr) || !$node->getAttribute($attr) instanceof $classname) {
            throw new LogicException("Failing attribute '$attr' in {$node->getType()}");
        }
    }

    public function beforeRequestOpeningRecordNode()
    {
        $this->output->write('01');
    }

    public function afterRequestOpeningRecordNode()
    {
        $this->output->write(self::EOL);
    }

    public function beforeDeleteMandateRequestNode()
    {
        $this->output->write('03');
    }

    public function afterDeleteMandateRequestNode()
    {
        $this->output->write(self::EOL);
    }
}
