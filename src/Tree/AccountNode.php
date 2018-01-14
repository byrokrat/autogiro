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

namespace byrokrat\autogiro\Tree;

use byrokrat\banking\AccountNumber;

/**
 * Wrapps a bank account number
 *
 * @see \byrokrat\autogiro\Visitor\AccountVisitor Creates attribute 'account'
 */
class AccountNode extends Node
{
    public static function fromAccount(AccountNumber $account): AccountNode
    {
        $node = new self(0, $account->getNumber());
        $node->setAttribute('account', $account);
        return $node;
    }
}
