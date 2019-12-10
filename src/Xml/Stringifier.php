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

namespace byrokrat\autogiro\Xml;

use Money\Money;
use Money\MoneyFormatter;

/**
 * Helper class to cast values to string
 */
class Stringifier
{
    /**
     * Arrays cast value
     */
    const ARRAY_VALUE = 'ARRAY_VALUE';

    /**
     * Object cast value
     */
    const OBJECT_VALUE = 'OBJECT_VALUE';

    /**
     * @var MoneyFormatter
     */
    private $moneyFormatter;

    public function __construct(MoneyFormatter $moneyFormatter)
    {
        $this->moneyFormatter = $moneyFormatter;
    }

    /**
     * Cast value to string
     *
     * @param mixed $value
     */
    public function stringify($value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if ($value instanceof Money) {
            return $this->moneyFormatter->format($value);
        }

        if (is_array($value)) {
            return self::ARRAY_VALUE;
        }

        if (is_object($value) && !method_exists($value, '__tostring')) {
            return self::OBJECT_VALUE;
        }

        return (string)$value;
    }
}
