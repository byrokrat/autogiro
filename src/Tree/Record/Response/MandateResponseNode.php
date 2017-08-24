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
 * Copyright 2016-17 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro\Tree\Record\Response;

use byrokrat\autogiro\Tree\Record\RecordNode;
use byrokrat\autogiro\Tree\AccountNode;
use byrokrat\autogiro\Tree\PayeeBankgiroNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\IdNode;
use byrokrat\autogiro\Tree\MessageNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\TextNode;
use byrokrat\autogiro\Messages;

/**
 * Feedback on the request to add, delete or update a mandate
 */
class MandateResponseNode extends RecordNode
{
    public function __construct(
        int $lineNr,
        PayeeBankgiroNode $payeeBg,
        PayerNumberNode $payerNr,
        AccountNode $account,
        IdNode $id,
        TextNode $space,
        MessageNode $info,
        MessageNode $status,
        DateNode $date,
        array $void = []
    ) {
        $this->setChild('payee_bankgiro', $payeeBg);
        $this->setChild('payer_number', $payerNr);
        $this->setChild('account', $account);
        $this->setChild('id', $id);
        $this->setChild('space_1', $space);
        $this->setChild('info', $info);
        $this->setChild('status', $status);
        $this->setChild('date', $date);
        parent::__construct($lineNr, $void);
    }
}
