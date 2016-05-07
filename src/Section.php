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

namespace byrokrat\autogiro;

/**
 * Basic ag container
 */
class Section
{
    /**
     * @var string Layout identifier
     */
    private $layoutIdentifier;

    /**
     * @var Record\Record Section opening record
     */
    private $openingRecord;

    /**
     * @var Record\Record Section closing record
     */
    private $closingRecord;

    /**
     * @var Record\Record[] Contained records
     */
    private $records = [];

    /**
     * Set layout identifier for this section
     *
     * @see Layouts For the list of valid layout identifiers
     */
    public function __construct(string $layoutIdentifier)
    {
        $this->layoutIdentifier = $layoutIdentifier;
        $this->openingRecord = new Record\NullRecord;
        $this->closingRecord = new Record\NullRecord;
    }

    /**
     * Get layout identifier for this section
     *
     * @see Layouts For the list of valid layout identifiers
     */
    public function getLayoutIdentifier(): string
    {
        return $this->layoutIdentifier;
    }

    /**
     * Set section opening record
     */
    public function setOpeningRecord(Record\Record $record)
    {
        $this->openingRecord = $record;
    }

    /**
     * Get section opening record
     */
    public function getOpeningRecord(): Record\Record
    {
        return $this->openingRecord;
    }

    /**
     * Set section closing record
     */
    public function setClosingRecord(Record\Record $record)
    {
        $this->closingRecord = $record;
    }

    /**
     * Get section closing record
     */
    public function getClosingRecord(): Record\Record
    {
        return $this->closingRecord;
    }

    /**
     * Add a contained record
     */
    public function addRecord(Record\Record $record)
    {
        $this->records[] = $record;
    }

    /**
     * Get contained records
     *
     * @return Record\Record[]
     */
    public function getRecords(): array
    {
        return $this->records;
    }
}
