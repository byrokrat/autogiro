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
