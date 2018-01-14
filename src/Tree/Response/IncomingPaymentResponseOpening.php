<?php
/**
 * This file is part of byrokrat\autogiro.
 *
 * byrokrat\autogiro is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * byrokrat\autogiro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with byrokrat\autogiro. If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2016-18 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro\Tree\Response;

use byrokrat\autogiro\Tree\AccountNode;
use byrokrat\autogiro\Tree\AmountNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\RecordNode;
use byrokrat\autogiro\Tree\TextNode;

/**
 * Header wrapping a set of incoming payment response records
 */
class IncomingPaymentResponseOpening extends RecordNode
{
    public function __construct(
        int $line,
        AccountNode $account,
        DateNode $date,
        TextNode $serial,
        AmountNode $amount,
        TextNode $recordCount,
        array $void = []
    ) {
        // TODO spec är ej updaterad med children...

        $this->setChild('account', $account);
        $this->setChild('date', $date);
        $this->setChild('serial', $serial);
        $this->setChild('amount', $amount);
        $this->setChild('record_count', $recordCount);
        parent::__construct($line, $void);
    }
}
