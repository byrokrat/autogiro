<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Parser\RecordReader;

use byrokrat\autogiro\Record;
use byrokrat\autogiro\Parser\Matcher;
use byrokrat\banking\AccountFactory;
use byrokrat\banking\BankNames;

/**
 * Default opening record reader for (a number of) new layouts
 */
class DefaultNewOpeningRecordReader extends RecordReader
{
    /**
     * TODO Tests missing; need a plan for how different readers should be organised
     */
    public function __construct(AccountFactory $accountFactory = null)
    {
        if (!$accountFactory) {
            $accountFactory = new AccountFactory;
            $accountFactory->whitelistFormats([BankNames::FORMAT_BANKGIRO]);
        }

        parent::__construct(
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
            function (array $values) use ($accountFactory) {
                return new Record\OpeningRecord(
                    trim($values['layout']),
                    \DateTimeImmutable::createFromFormat('Ymd', $values['date']),
                    $accountFactory->createAccount(ltrim($values['bankgiro'], '0')),
                    ltrim($values['customerNr'], '0')
                );
            }
        );
    }
}
