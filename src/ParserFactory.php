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

use byrokrat\autogiro\Processor\AccountProcessor;
use byrokrat\autogiro\Processor\AmountProcessor;
use byrokrat\autogiro\Processor\DateProcessor;
use byrokrat\autogiro\Processor\IdProcessor;
use byrokrat\autogiro\Processor\LayoutProcessor;
use byrokrat\autogiro\Processor\MessageProcessor;
use byrokrat\autogiro\Processor\MultiCore;
use byrokrat\autogiro\Processor\PayeeProcessor;
use byrokrat\autogiro\Processor\TextProcessor;
use byrokrat\autogiro\Processor\TransactionProcessor;
use byrokrat\id\CoordinationIdFactory;
use byrokrat\id\NullIdFactory;
use byrokrat\id\OrganizationIdFactory;
use byrokrat\id\PersonalIdFactory;
use byrokrat\banking\AccountFactory;
use byrokrat\banking\Formats as AccountFormats;

/**
 * Simplifies the creation of parser objects
 */
class ParserFactory implements Visitors
{
    /**
     * Create a new parser
     */
    public function createParser(int $flags = 0): Parser
    {
        $flag = function (int $needle) use ($flags) {
            return ($needle & $flags) == $needle;
        };

        $processors = new MultiCore(
            new DateProcessor,
            new LayoutProcessor,
            new MessageProcessor,
            new PayeeProcessor,
            new TextProcessor,
            new TransactionProcessor
        );

        if (!$flag(self::VISITOR_IGNORE_ACCOUNTS)) {
            $accountFactory = new AccountFactory;
            $accountFactory->blacklistFormats([AccountFormats::FORMAT_PLUSGIRO]);

            $bankgiroFactory = new AccountFactory;
            $bankgiroFactory->whitelistFormats([AccountFormats::FORMAT_BANKGIRO]);

            $processors->addProcessor(new AccountProcessor($accountFactory, $bankgiroFactory));
        }

        if (!$flag(self::VISITOR_IGNORE_AMOUNTS)) {
            $processors->addProcessor(new AmountProcessor);
        }

        if (!$flag(self::VISITOR_IGNORE_IDS)) {
            $processors->addProcessor(
                new IdProcessor(
                    new OrganizationIdFactory,
                    new PersonalIdFactory(new CoordinationIdFactory(new NullIdFactory))
                )
            );
        }

        return new Parser(new Grammar, $processors);
    }
}
