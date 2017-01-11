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
    private $payeeBgcNr;

    /**
     * @var string Payee bankgiro account number
     */
    private $payeeBankgiro;

    /**
     * @var string Date of file creation (yyyymmdd)
     */
    private $date;

    /**
     * @param string $payeeBgcNr    The BGC customer number of payee
     * @param string $payeeBankgiro The bankgiro number of payee
     * @param string $date          Optional creation date formatted as yyyymmdd
     */
    public function __construct(string $payeeBgcNr, string $payeeBankgiro, string $date = '')
    {
        $this->payeeBgcNr = $payeeBgcNr;
        $this->payeeBankgiro = $payeeBankgiro;
        $this->date = $date ?: date('Ymd');
        $this->reset();
    }

    /**
     * Reset builder to initial state
     */
    public function reset()
    {
        $this->opening = new RequestOpeningRecordNode(
            0,
            new DateNode(0, $this->date),
            new TextNode(0, 'AUTOGIRO'),
            new TextNode(0, str_pad('', 44)),
            new PayeeBgcNumberNode(0, $this->payeeBgcNr),
            new PayeeBankgiroNode(0, $this->payeeBankgiro),
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
            new PayeeBankgiroNode(0, $this->payeeBankgiro),
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
