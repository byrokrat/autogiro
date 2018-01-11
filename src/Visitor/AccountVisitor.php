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

use byrokrat\autogiro\Tree\AccountNode;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\BankgiroNode;
use byrokrat\autogiro\Tree\ReferredAccountNode;
use byrokrat\banking\AccountFactory;
use byrokrat\banking\Exception as BankingException;

/**
 * Validates the structure of account numbers in tree
 */
class AccountVisitor extends ErrorAwareVisitor
{
    /**
     * @var AccountFactory
     */
    private $accountFactory;

    /**
     * @var AccountFactory
     */
    private $bankgiroFactory;

    public function __construct(ErrorObject $errorObj, AccountFactory $accountFactory, AccountFactory $bankgiroFactory)
    {
        parent::__construct($errorObj);
        $this->accountFactory = $accountFactory;
        $this->bankgiroFactory = $bankgiroFactory;
    }

    public function beforeAccountNode(AccountNode $node): void
    {
        $this->writeAccountAttr($node->getValue(), $node, $this->accountFactory);
    }

    public function beforeBankgiroNode(BankgiroNode $node): void
    {
        $this->writeAccountAttr($node->getValue(), $node, $this->bankgiroFactory);
    }

    public function beforeReferredAccountNode(ReferredAccountNode $node): void
    {
        if (!$node->hasAttribute('referred_value')) {
            return;
        }

        $this->writeAccountAttr($node->getAttribute('referred_value'), $node, $this->accountFactory);
    }

    private function writeAccountAttr(string $number, Node $node, AccountFactory $factory): void
    {
        if ($node->hasAttribute('account')) {
            return;
        }

        try {
            $node->setAttribute(
                'account',
                $factory->createAccount($number)
            );
        } catch (BankingException $e) {
            $this->getErrorObject()->addError(
                "Invalid account number %s on line %s",
                $number,
                (string)$node->getLineNr()
            );
        }
    }
}
