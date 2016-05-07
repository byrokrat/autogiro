<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Parser\Strategy;

use byrokrat\autogiro\Parser\StateMachine;
use byrokrat\autogiro\Parser\Matcher;
use byrokrat\autogiro\Line;

/**
 * Strategy for parsing responses to previously made mandate requests
 */
class MandateResponseStrategy implements Strategy
{
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
        $this->records = [];
    }

    public function on01(Line $line)
    {
        /*
            TODO Readers kan olika beroende av om det är ny eller gammal layout
                därför ska detta vara DI, så kan jag bygga en strategi som passar med
                filen jag har framför mig..
         */
        $openingRecordReader = new \byrokrat\autogiro\Parser\RecordReader(
            [
                'tc' => new Matcher\Text(1, '01'),
                'autogiro' => new Matcher\Text(3, str_pad('AUTOGIRO', 20, ' ')),
                'backup1' => new Matcher\Space(23, 2),
                'date' => new Matcher\Number(25, 8),
                'backup2' => new Matcher\Space(33, 12),
                'layout' => new Matcher\Text(45, str_pad('AG-MEDAVI', 20, ' ')),
                'customerNr' => new Matcher\Number(65, 6),
                'bankgiro' => new Matcher\Number(71, 10),
            ],
            function (array $values) {
                // TODO ska såklart vara DI...
                $factory = new \byrokrat\banking\AccountFactory;
                $factory->whitelistFormats([\byrokrat\banking\BankNames::FORMAT_BANKGIRO]);

                return new \byrokrat\autogiro\Record\OpeningRecord(
                    trim($values['layout']),
                    \DateTimeImmutable::createFromFormat('Ymd', $values['date']),
                    $factory->createAccount(ltrim($values['bankgiro'], '0')),
                    ltrim($values['customerNr'], '0')
                );
            }
        );

        // TODO definiera en Section som första steg för att definiera Parser return value
            // kan vara riktigt enkel...
        // TODO skriv test för Parser
        // TODO börjar det se bra ut? Utvärdera och tänk...

        // TODO nu är det tydligen självklart så att asylgrp har gammal layout..
            // innan jag börjar hårdjobba med format osv måste jag ha testing strategy på plats!!

        $this->records[] = $openingRecordReader->readRecord($line);
    }

    public function on73(Line $line)
    {
    }

    public function on09(Line $line)
    {
    }

    public function done(): array
    {
        return $this->records;
    }
}
