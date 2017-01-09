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

namespace byrokrat\autogiro\Tree;

/**
 * Node representing a transaction interval
 */
class IntervalNode extends TextNode
{
    /**
     * Perform transaction once
     */
    const INTERVAL_ONCE = '0';

    /**
     * Perform transaction once every month on the date specified in record
     */
    const INTERVAL_MONTHLY_ON_DATE = '1';

    /**
     * Perform transaction once every quarter on the date specified in record
     */
    const INTERVAL_QUARTERLY_ON_DATE = '2';

    /**
     * Perform transaction once every six months on the date specified in record
     */
    const INTERVAL_SEMIANUALLY_ON_DATE = '3';

    /**
     * Perform transaction once every year on the date specified in record
     */
    const INTERVAL_ANUALLY_ON_DATE = '4';

    /**
     * Perform transaction once every month on the last calender day
     */
    const INTERVAL_MONTHLY_ON_LAST_CALENDAR_DAY = '5';

    /**
     * Perform transaction once every quarter on the last calender day
     */
    const INTERVAL_QUARTERLY_ON_LAST_CALENDAR_DAY = '6';

    /**
     * Perform transaction once every six months on the last calender day
     */
    const INTERVAL_SEMIANUALLY_ON_LAST_CALENDAR_DAY = '7';

    /**
     * Perform transaction once every year on the last calender day
     */
    const INTERVAL_ANUALLY_ON_LAST_CALENDAR_DAY = '8';

    public function __construct(int $lineNr = 0, string $value = '')
    {
        parent::__construct($lineNr, $value, '/^[0-8]$/');
    }
}
