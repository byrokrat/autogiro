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

namespace byrokrat\autogiro;

/**
 * Visitor creation flags
 *
 * TODO kan flyttas till VisitorFactory så länge som ParserFactory ärver VisitorFactory...
 */
interface Visitors
{
    /**
     * Do not include account number visitor
     */
    const VISITOR_IGNORE_ACCOUNTS = 1;

    /**
     * Do not include amount visitor
     */
    const VISITOR_IGNORE_AMOUNTS = 2;

    /**
     * Do not include id visitor
     */
    const VISITOR_IGNORE_IDS = 4;

    /**
     * Ignore all visitors based on external dependencies
     */
    const VISITOR_IGNORE_EXTERNAL = self::VISITOR_IGNORE_ACCOUNTS | self::VISITOR_IGNORE_AMOUNTS | self::VISITOR_IGNORE_IDS;
}
