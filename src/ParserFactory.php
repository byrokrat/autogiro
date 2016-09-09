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

namespace byrokrat\autogiro;

use byrokrat\autogiro\Visitor\ValidatingVisitor;
use byrokrat\banking;
use byrokrat\id;

/**
 * Simplifies the creation of parser objects
 */
class ParserFactory
{
    /**
     * Create a new parser
     */
    public function createParser(): Parser
    {
        $accountFactory = new banking\AccountFactory;
        $accountFactory->blacklistFormats([banking\Formats::FORMAT_PLUSGIRO]);

        return new Parser(
            new Grammar(
                $accountFactory,
                new banking\BankgiroFactory,
                new id\PersonalIdFactory(new id\CoordinationIdFactory(new id\NullIdFactory)),
                new id\OrganizationIdFactory,
                new Message\MessageFactory
            ),
            new ValidatingVisitor
        );
    }
}
