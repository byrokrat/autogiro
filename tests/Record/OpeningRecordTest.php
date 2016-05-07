<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Record;

class OpeningRecordTest extends \PHPUnit_Framework_TestCase
{
    public function testValues()
    {
        $layout = 'layout';
        $date = new \DateTimeImmutable;
        $bankgiro = $this->prophesize('byrokrat\banking\Bankgiro')->reveal();
        $customerNr = 'nr';

        $record = new OpeningRecord($layout, $date, $bankgiro, $customerNr);

        $this->assertSame(
            $layout,
            $record['layout']
        );

        $this->assertSame(
            $date,
            $record['date']
        );

        $this->assertSame(
            $bankgiro,
            $record['bankgiro']
        );

        $this->assertSame(
            $customerNr,
            $record['customerNr']
        );
    }
}
