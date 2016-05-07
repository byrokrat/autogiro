<?php
/**
 * This file is part of byrokrat/autogiro.
 *
 * byrokrat/autogiro is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * byrokrat/autogiro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with byrokrat/autogiro. If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2016 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro\Parser\Matcher;

use byrokrat\autogiro\Line;
use byrokrat\autogiro\Exception\InvalidContentException;

/**
 * Grab content of the first valid sub-matcher
 */
class FirstOf implements Matcher
{
    /**
     * @var Matcher[] Loaded matchers
     */
    private $matchers;

    public function __construct(Matcher ...$matchers)
    {
        $this->matchers = $matchers;
    }

    public function match(Line $line): string
    {
        foreach ($this->matchers as $matcher) {
            try {
                return $matcher->match($line);
            } catch (InvalidContentException $exception) {
                // Skipping invalid matches in favour of first valid match
            }
        }

        throw new InvalidContentException(
            'Unable to find a match using a set of matchers'
        );
    }
}
