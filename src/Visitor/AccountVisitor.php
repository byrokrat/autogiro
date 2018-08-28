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

use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\Obj;
use byrokrat\banking\AccountFactoryInterface;
use byrokrat\banking\Exception as BankingException;

/**
 * Validates the structure of account numbers in tree
 *
 * Creates account object as child 'Object'
 */
class AccountVisitor extends ErrorAwareVisitor
{
    /**
     * @var AccountFactoryInterface
     */
    private $accountFactory;

    /**
     * @var AccountFactoryInterface
     */
    private $bankgiroFactory;

    public function __construct(
        ErrorObject $errorObj,
        AccountFactoryInterface $accountFactory,
        AccountFactoryInterface $bankgiroFactory
    ) {
        parent::__construct($errorObj);
        $this->accountFactory = $accountFactory;
        $this->bankgiroFactory = $bankgiroFactory;
    }

    public function beforeAccount(Node $node): void
    {
        $this->writeAccount($node, $this->accountFactory);
    }

    public function beforePayeeBankgiro(Node $node): void
    {
        $this->writeAccount($node, $this->bankgiroFactory);
    }

    private function writeAccount(Node $node, AccountFactoryInterface $factory): void
    {
        if ($node->hasChild('Object')) {
            return;
        }

        $number = (string)$node->getChild('Number')->getValue();

        if (trim($number, '0') == '') {
            return;
        }

        try {
            $node->addChild(new Obj($node->getLineNr(), $factory->createAccount($number)));
        } catch (BankingException $e) {
            $this->getErrorObject()->addError(
                "Invalid account number %s (%s) on line %s",
                $number,
                $e->getMessage(),
                (string)$node->getLineNr()
            );
        }
    }
}
