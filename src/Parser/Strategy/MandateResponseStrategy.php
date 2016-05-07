<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Parser\Strategy;

use byrokrat\autogiro\Layouts;
use byrokrat\autogiro\Section;
use byrokrat\autogiro\Line;
use byrokrat\autogiro\Parser\RecordReader;
use byrokrat\autogiro\Parser\StateMachine;

/**
 * Strategy for parsing responses to previously made mandate requests
 */
class MandateResponseStrategy implements Strategy, Layouts
{
    /**
     * @var Section
     */
    private $section;

    /**
     * @var RecordReader\RecordReader;
     */
    private $openingRecordReader;

    public function __construct(RecordReader\RecordReader $openingRecordReader = null)
    {
        // TODO When we have a factory pattern for creating strategies there should be no default reader here...
        $this->openingRecordReader = $openingRecordReader ?: new RecordReader\DefaultNewOpeningRecordReader;
    }

    public function createStates(): StateMachine
    {
        return new StateMachine([
            StateMachine::STATE_INIT => ['01'],
            '01' => ['73', '09'],
            '73' => ['73', '09'],
            '09' => [StateMachine::STATE_DONE]
        ]);
    }

    public function begin()
    {
        $this->section = new Section(self::LAYOUT_MANDATE_RESPONSE);
    }

    public function on01(Line $line)
    {
        $this->section->setOpeningRecord($this->openingRecordReader->readRecord($line));
    }

    public function on73(Line $line)
    {
        // TODO implement parsing 73-records
    }

    public function on09(Line $line)
    {
        // TODO implement parsing 09-records
    }

    public function done(): Section
    {
        return $this->section;
    }
}
