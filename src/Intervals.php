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

namespace byrokrat\autogiro;

/**
 * Collection of valid payment interval ids
 */
interface Intervals
{
    /**
     * Perform payment once
     */
    public const INTERVAL_ONCE = '0';

    /**
     * Perform payment once every month on the date specified in record
     */
    public const INTERVAL_MONTHLY_ON_DATE = '1';

    /**
     * Perform payment once every quarter on the date specified in record
     */
    public const INTERVAL_QUARTERLY_ON_DATE = '2';

    /**
     * Perform payment once every six months on the date specified in record
     */
    public const INTERVAL_SEMIANUALLY_ON_DATE = '3';

    /**
     * Perform payment once every year on the date specified in record
     */
    public const INTERVAL_ANUALLY_ON_DATE = '4';

    /**
     * Perform payment once every month on the last calender day
     */
    public const INTERVAL_MONTHLY_ON_LAST_CALENDAR_DAY = '5';

    /**
     * Perform payment once every quarter on the last calender day
     */
    public const INTERVAL_QUARTERLY_ON_LAST_CALENDAR_DAY = '6';

    /**
     * Perform payment once every six months on the last calender day
     */
    public const INTERVAL_SEMIANUALLY_ON_LAST_CALENDAR_DAY = '7';

    /**
     * Perform payment once every year on the last calender day
     */
    public const INTERVAL_ANUALLY_ON_LAST_CALENDAR_DAY = '8';
}
