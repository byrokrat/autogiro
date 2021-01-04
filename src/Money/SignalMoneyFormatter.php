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

namespace byrokrat\autogiro\Money;

use byrokrat\autogiro\Exception\RuntimeException;
use Money\Money;
use Money\MoneyFormatter;

final class SignalMoneyFormatter implements MoneyFormatter
{
    private const SIGNALS = [
        '0' => 'å',
        '1' => 'J',
        '2' => 'K',
        '3' => 'L',
        '4' => 'M',
        '5' => 'N',
        '6' => 'O',
        '7' => 'P',
        '8' => 'Q',
        '9' => 'R',
    ];

    public function format(Money $money)
    {
        if ($money->getCurrency()->getCode() != 'SEK') {
            throw new RuntimeException('SignalMoneyFormatter can only work with SEK');
        }

        $amount = $money->getAmount();

        if ($money->isPositive()) {
            return $amount;
        }

        $lastChar = substr($amount, -1);

        if (isset(self::SIGNALS[$lastChar])) {
            return substr($amount, 1, -1) . self::SIGNALS[$lastChar];
        }

        throw new \LogicException("Unknown signal amount ending char: $lastChar");
    }
}
