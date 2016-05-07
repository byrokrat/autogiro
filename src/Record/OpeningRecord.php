<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Record;

use byrokrat\banking\Bankgiro;

/**
 * Standard opening record implementation
 */
class OpeningRecord extends Record
{
    public function __construct(string $layout, \DateTimeImmutable $date, Bankgiro $bankgiro, string $customerNr = '')
    {
        $this['layout'] = $layout;
        $this['date'] = $date;
        $this['bankgiro'] = $bankgiro;
        $this['customerNr'] = $customerNr;
    }
}
