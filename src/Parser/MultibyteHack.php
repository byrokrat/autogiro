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

namespace byrokrat\autogiro\Parser;

/**
 * Hack to make the phpeg Grammar use multibyte processing
 *
 * By extending this class the strlen() and substr() functions will automatically
 * be imported to the Parser namespace and replace the standard functions with
 * their multibyte equivalents.
 */
class MultibyteHack
{
}

/**
 * @param string $str
 * @return int
 */
function strlen($str)
{
    return mb_strlen($str);
}

/**
 * @param string $str
 * @param int $start
 * @param int $length
 * @return string
 */
function substr($str, $start, $length)
{
    return mb_substr($str, $start, $length);
}
