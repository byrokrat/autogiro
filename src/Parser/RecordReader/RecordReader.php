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

namespace byrokrat\autogiro\Parser\RecordReader;

use byrokrat\autogiro\Line;
use byrokrat\autogiro\Record\Record;
use byrokrat\autogiro\Parser\Matcher;

/**
 * Match and capture parts of a line into a Record
 */
class RecordReader
{
    /**
     * @var Matcher\Matcher[]
     */
    private $matchers = [];

    /**
     * @var callable Record builder
     */
    private $builder;

    /**
     * Inject matchers and builder
     *
     * The builder should take an array of values captured by registered
     * matchers and return a Record object.
     */
    public function __construct(array $matchers, callable $builder)
    {
        foreach ($matchers as $key => $matcher) {
            $this->addMatcher($key, $matcher);
        }
        $this->setBuilder($builder);
    }

    /**
     * Create Record from matched $line content
     */
    public function readRecord(Line $line): Record
    {
        $parts = [];

        foreach ($this->matchers as $key => $matcher) {
            $parts[$key] = $matcher->match($line);
        }

        return ($this->builder)($parts);
    }

    /**
     * Add content matcher
     */
    protected function addMatcher(string $key, Matcher\Matcher $matcher)
    {
        $this->matchers[$key] = $matcher;
    }

    /**
     * Set record builder
     */
    protected function setBuilder(callable $builder)
    {
        $this->builder = $builder;
    }
}
