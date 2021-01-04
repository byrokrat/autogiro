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
 * Copyright 2016-21 Hannes Forsgård
 */

declare(strict_types=1);

namespace byrokrat\autogiro\Writer;

use byrokrat\autogiro\Money\SignalMoneyFormatter;
use byrokrat\banking\AccountNumber;

final class WriterFactory
{
    /**
     * @param string             $bgcNr    The BGC customer number of payee
     * @param AccountNumber      $bankgiro Payee bankgiro account number
     * @param \DateTimeInterface $date     Optional creation date
     */
    public function createWriter(
        string $bgcNr,
        AccountNumber $bankgiro,
        \DateTimeInterface $date = null
    ): WriterInterface {
        return new Writer(
            new TreeBuilder(
                $bgcNr,
                $bankgiro,
                $date ?: new \DateTimeImmutable()
            ),
            new PrintingVisitor(
                new SignalMoneyFormatter()
            )
        );
    }
}
