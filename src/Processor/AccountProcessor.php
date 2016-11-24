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

use byrokrat\autogiro\Tree\Account\AccountNode;
use byrokrat\autogiro\Tree\Account\BankgiroNode;
use byrokrat\banking\AccountFactory;
use byrokrat\banking\Exception as BankingException;

/**
 * Validates the structure of account numbers in tree
 */
class AccountProcessor extends Processor
{
    /**
     * @var AccountFactory
     */
    private $accountFactory;

    /**
     * @var AccountFactory
     */
    private $bankgiroFactory;

    public function __construct(AccountFactory $accountFactory, AccountFactory $bankgiroFactory)
    {
        $this->accountFactory = $accountFactory;
        $this->bankgiroFactory = $bankgiroFactory;
    }

    public function beforeAccountNode(AccountNode $node)
    {
        try {
            $node->setAttribute(
                'account',
                $this->accountFactory->createAccount($node->getValue())
            );
        } catch (BankingException $e) {
            $this->addError(
                "Invalid account number %s on line %s",
                $node->getValue(),
                (string)$node->getLineNr()
            );
        }
    }

    public function beforeBankgiroNode(BankgiroNode $node)
    {
        try {
            $node->setAttribute(
                'account',
                $this->bankgiroFactory->createAccount($node->getValue())
            );
        } catch (BankingException $e) {
            $this->addError(
                "Invalid bankgiro number %s on line %s",
                $node->getValue(),
                (string)$node->getLineNr()
            );
        }
    }
}
