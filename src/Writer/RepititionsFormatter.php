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
 * Copyright 2016-17 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro\Writer;

use byrokrat\autogiro\Exception\LogicException;

/**
 * Format number of repititions according to autogiro specs
 */
class RepititionsFormatter
{
    public function format(int $repititions): string
    {
        if ($repititions > 999 || $repititions < 0) {
            throw new LogicException("Invalid number of repitions: $repititions");
        }

        if (0 == $repititions) {
            return '   ';
        }

        return str_pad((string)$repititions, 3, '0', STR_PAD_LEFT);
    }
}
