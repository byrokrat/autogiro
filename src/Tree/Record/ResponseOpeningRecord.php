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

namespace byrokrat\autogiro\Tree\Record;

use byrokrat\autogiro\Tree\PayeeBankgiroNode;
use byrokrat\autogiro\Tree\PayeeBgcNumberNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\TextNode;

/**
 * Response opening record node
 */
class ResponseOpeningRecord extends RecordNode
{
    public function __construct(
        int $lineNr,
        TextNode $agTxt,
        TextNode $space1,
        DateNode $date,
        TextNode $space2,
        TextNode $layoutName,
        PayeeBgcNumberNode $payeeBgcNr,
        PayeeBankgiroNode $payeeBg,
        array $void = []
    ) {
        $this->setChild('autogiro_txt', $agTxt);
        $this->setChild('space_1', $space1);
        $this->setChild('date', $date);
        $this->setChild('space_2', $space2);
        $this->setChild('layout_name', $layoutName);
        $this->setChild('payee_bgc_number', $payeeBgcNr);
        $this->setChild('payee_bankgiro', $payeeBg);
        parent::__construct($lineNr, $void);
    }
}
