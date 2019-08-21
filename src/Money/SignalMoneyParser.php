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
 * Copyright 2016-19 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro\Money;

use byrokrat\autogiro\Exception\RuntimeException;
use Money\Money;
use Money\MoneyParser;

final class SignalMoneyParser implements MoneyParser
{
    private const SIGNALS = [
        'å' => '0',
        'J' => '1',
        'K' => '2',
        'L' => '3',
        'M' => '4',
        'N' => '5',
        'O' => '6',
        'P' => '7',
        'Q' => '8',
        'R' => '9',
    ];

    public function parse($money, $forceCurrency = null)
    {
        if (!is_string($money)) {
            throw new \InvalidArgumentException('Money must be a string');
        }

        if (empty($money)) {
            throw new RuntimeException('Money must not be empty');
        }

        $money = ltrim($money, '0');

        if (empty($money)) {
            $money = '0';
        }

        $lastChar = mb_substr($money, -1);

        if (isset(self::SIGNALS[$lastChar])) {
            $money = sprintf(
                '-%s%s',
                mb_substr($money, 0, mb_strlen($money)-1),
                self::SIGNALS[$lastChar]
            );
        }

        if (!preg_match('/^\-?[0-9]+$/', $money)) {
            throw new RuntimeException('Money is not a valid singal string');
        }

        return Money::SEK($money);
    }
}
