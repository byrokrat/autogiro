<?php

declare(strict_types=1);

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
