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
 * Base implementation of the Matcher interface
 */
abstract class BaseMatcher implements Matcher
{
    /**
     * @var int Start matching from
     */
    private $startPos;

    /**
     * @var int Length to match
     */
    private $length;

    /**
     * Set match area
     *
     * Note that to make matcher definitions and the technical specifications of
     * autoirot appear as similar as possible the first character of a line is
     * regarded as being in position one (1).
     */
    public function __construct(int $startPos, int $length)
    {
        $this->startPos = $startPos;
        $this->length = $length;
    }

    /**
     * Get a description of the expected content
     */
    abstract protected function getDescription(): string;

    /**
     * Check if string is valid according to matching rules
     */
    abstract protected function isMatch(string $str): bool;

    /**
     * Match line and grab substring on success
     *
     * @throws InvalidContentException if line does not match
     */
    public function match(Line $line): string
    {
        $str = $line->substr($this->startPos - 1, $this->length);

        if (!$this->isMatch($str)) {
            throw new InvalidContentException(
                sprintf(
                    "Invalid content '%s' (expecting %s) starting at position %s",
                    $str,
                    $this->getDescription(),
                    $this->startPos - 1
                )
            );
        }

        return $str;
    }
}
