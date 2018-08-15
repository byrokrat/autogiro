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

use byrokrat\id\CoordinationIdFactory;
use byrokrat\id\NullIdFactory;
use byrokrat\id\OrganizationIdFactory;
use byrokrat\id\PersonalIdFactory;
use byrokrat\banking\AccountFactory;
use byrokrat\banking\BankgiroFactory;
use byrokrat\banking\DelegatingFactory;

/**
 * Creates the set of standard visitors
 */
class VisitorFactory
{
    /**
     * Do not include account number visitor
     */
    const VISITOR_IGNORE_ACCOUNTS = 1;

    /**
     * Do not include amount visitor
     */
    const VISITOR_IGNORE_AMOUNTS = 2;

    /**
     * Do not include id visitor
     */
    const VISITOR_IGNORE_IDS = 4;

    /**
     * Ignore all visitors based on external dependencies
     */
    const VISITOR_IGNORE_EXTERNAL = self::VISITOR_IGNORE_ACCOUNTS
        | self::VISITOR_IGNORE_AMOUNTS
        | self::VISITOR_IGNORE_IDS;

    /**
     * Create the standard set of visitors used when processing a parse tree
     */
    public function createVisitors(int $flags = 0): VisitorInterface
    {
        $flag = function (int $needle) use ($flags) {
            return ($needle & $flags) == $needle;
        };

        $errorObj = new ErrorObject;

        $container = new VisitorContainer(
            $errorObj,
            new DateVisitor($errorObj),
            new MessageVisitor($errorObj),
            new PayeeVisitor($errorObj),
            new TextVisitor($errorObj),
            new PaymentVisitor($errorObj)
        );

        if (!$flag(self::VISITOR_IGNORE_ACCOUNTS)) {
            $container->addVisitor(
                new AccountVisitor(
                    $errorObj,
                    new DelegatingFactory(new AccountFactory, new BankgiroFactory),
                    new BankgiroFactory
                )
            );
        }

        if (!$flag(self::VISITOR_IGNORE_AMOUNTS)) {
            $container->addVisitor(new AmountVisitor($errorObj));
        }

        if (!$flag(self::VISITOR_IGNORE_IDS)) {
            $container->addVisitor(
                new IdVisitor(
                    $errorObj,
                    new OrganizationIdFactory,
                    new PersonalIdFactory(new CoordinationIdFactory(new NullIdFactory))
                )
            );
        }

        return $container;
    }
}
