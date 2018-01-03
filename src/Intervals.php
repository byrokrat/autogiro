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
 * Copyright 2016-18 Hannes Forsgård
 */

namespace byrokrat\autogiro;

/**
 * Collection of valid interval ids
 */
interface Intervals
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

    /**
     * Map of constants to descriptions
     */
    const MESSAGE_MAP = [
        self::INTERVAL_ONCE                             => 'En gång',
        self::INTERVAL_MONTHLY_ON_DATE                  => 'En gång per månad på den kalenderdag som anges i betalningsposten.',
        self::INTERVAL_QUARTERLY_ON_DATE                => 'En gång per kvartal på den kalenderdag som anges i betalningsposten.',
        self::INTERVAL_SEMIANUALLY_ON_DATE              => 'En gång per halvår på den kalenderdag som anges i betalningsposten.',
        self::INTERVAL_ANUALLY_ON_DATE                  => 'En gång per år på den kalenderdag som anges i betalningsposten.',
        self::INTERVAL_MONTHLY_ON_LAST_CALENDAR_DAY     => 'En gång per månad på sista möjliga bankdag.',
        self::INTERVAL_QUARTERLY_ON_LAST_CALENDAR_DAY   => 'En gång per kvartal på sista möjliga bankdag.',
        self::INTERVAL_SEMIANUALLY_ON_LAST_CALENDAR_DAY => 'En gång per halvår på sista möjliga bankdag.',
        self::INTERVAL_ANUALLY_ON_LAST_CALENDAR_DAY     => 'En gång per år på sista möjliga bankdag.'
    ];
}
