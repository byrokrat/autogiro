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

namespace byrokrat\autogiro\Writer;

use byrokrat\autogiro\Tree\Record\RecordNode;
use byrokrat\autogiro\Tree\Record\Request\RequestOpeningRecordNode;
use byrokrat\autogiro\Tree\Record\Request\DeleteMandateRequestNode;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\TextNode;
use byrokrat\autogiro\Tree\PayeeBgcNumberNode;
use byrokrat\autogiro\Tree\PayeeBankgiroNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\banking\Bankgiro;

/**
 * Build trees representing autogiro request files
 */
class TreeBuilder
{
    /**
     * @var RequestOpeningRecordNode Opening record used for each layout
     */
    private $opening;

    /**
     * @var RecordNode[] List of created mandate records
     */
    private $mandateRecords;

    /**
     * @var RecordNode[] List of created transaction records
     */
    private $transactionRecords;

    /**
     * @var RecordNode[] List of created amendment records
     */
    private $amendmentRecords;

    /**
     * @var string Payee BGC customer number
     */
    private $bgcNr;

    /**
     * @var Bankgiro Payee bankgiro account number
     */
    private $bankgiro;

    /**
     * @var \DateTimeInterface Date of file creation
     */
    private $date;

    /**
     * @param string             $bgcNr    The BGC customer number of payee
     * @param Bankgiro           $bankgiro Payee bankgiro account number
     * @param \DateTimeInterface $date     Optional creation date
     */
    public function __construct(string $bgcNr, Bankgiro $bankgiro, \DateTimeInterface $date = null)
    {
        $this->bgcNr = $bgcNr;
        $this->bankgiro = $bankgiro;
        $this->date = $date ?: new \DateTimeImmutable;
        $this->reset();
    }

    /**
     * Reset builder to initial state
     */
    public function reset()
    {
        $this->opening = new RequestOpeningRecordNode(
            0,
            (new DateNode(0, $this->date->format('Ymd')))->setAttribute('date', $this->date),
            new TextNode(0, 'AUTOGIRO'),
            new TextNode(0, str_pad('', 44)),
            new PayeeBgcNumberNode(0, $this->bgcNr),
            (new PayeeBankgiroNode(0, $this->bankgiro->getNumber()))->setAttribute('account', $this->bankgiro),
            [new TextNode(0, '  ')]
        );
        $this->mandateRecords = [];
        $this->transactionRecords = [];
        $this->amendmentRecords = [];
    }

    /**
     * Add a delete mandate record to tree
     */
    public function addDeleteMandateRecord(string $payerNr)
    {
        $this->mandateRecords[] = new DeleteMandateRequestNode(
            0,
            (new PayeeBankgiroNode(0, $this->bankgiro->getNumber()))->setAttribute('account', $this->bankgiro),
            new PayerNumberNode(0, $payerNr),
            [new TextNode(0, str_pad('', 52))]
        );
    }

    /**
     * Get the created request tree
     */
    public function buildTree(): FileNode
    {
        $layouts = [];

        foreach (['mandateRecords', 'transactionRecords', 'amendmentRecords'] as $records) {
            if (!empty($this->$records)) {
                $layouts[] = new LayoutNode($this->opening, ...$this->$records);
            }
        }

        return new FileNode(...$layouts);
    }
}
