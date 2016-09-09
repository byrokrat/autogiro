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
 * Copyright 2016 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro\Tree;

use byrokrat\autogiro\Message\Message;
use byrokrat\banking\AccountNumber;
use byrokrat\banking\Bankgiro;
use byrokrat\id\Id;

/**
 * Value object wrapping data for a new mandate
 */
class MandateResponseNode implements NodeInterface
{
    use Attr\BankgiroAttribute, Attr\DateAttribute, Attr\LineNrAttribute, Attr\MessageAttribute;

    /**
     * @var string
     */
    private $payerNr;

    /**
     * @var AccountNumber
     */
    private $account;

    /**
     * @var Id
     */
    private $id;

    /**
     * Load values at construct
     */
    public function __construct(
        Bankgiro $bankgiro,
        string $payerNr,
        AccountNumber $account,
        Id $id,
        Message $info,
        Message $comment,
        \DateTimeInterface $date,
        int $lineNr
    ) {
        $this->bankgiro = $bankgiro;
        $this->payerNr = $payerNr;
        $this->account = $account;
        $this->id = $id;
        $this->messages[] = $info;
        $this->messages[] = $comment;
        $this->date = $date;
        $this->lineNr = $lineNr;
    }

    /**
     * Get loaded payer number
     */
    public function getPayerNumber(): string
    {
        return $this->payerNr;
    }

    /**
     * Get loaded account number
     */
    public function getAccount(): AccountNumber
    {
        return $this->account;
    }

    /**
     * Get loaded state id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * Accept a visitor
     */
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visitMandateResponseNode($this);
    }
}
