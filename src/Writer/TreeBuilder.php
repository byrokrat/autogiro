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
use byrokrat\autogiro\Tree\Record\Request\AcceptMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\CreateMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\DeleteMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\RejectMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\UpdateMandateRequestNode;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\TextNode;
use byrokrat\autogiro\Tree\PayeeBgcNumberNode;
use byrokrat\autogiro\Tree\PayeeBankgiroNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\AccountNode;
use byrokrat\autogiro\Tree\IdNode;
use byrokrat\banking\AccountNumber;
use byrokrat\banking\Bankgiro;
use byrokrat\id\Id;

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
     * @var PayeeBankgiroNode Wrapper around payee bankgiro account number
     */
    private $payeeBgNode;

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
        $this->payeeBgNode = (new PayeeBankgiroNode(0, $bankgiro->getNumber()))->setAttribute('account', $bankgiro);
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
            $this->payeeBgNode,
            [new TextNode(0, '  ')]
        );
        $this->mandateRecords = [];
        $this->transactionRecords = [];
        $this->amendmentRecords = [];
    }

    /**
     * Add a new mandate record to tree
     */
    public function addCreateMandateRecord(string $payerNr, AccountNumber $account, Id $id)
    {
        $this->mandateRecords[] = new CreateMandateRequestNode(
            0,
            $this->payeeBgNode,
            new PayerNumberNode(0, $payerNr),
            (new AccountNode(0, $account->getNumber()))->setAttribute('account', $account),
            (new IdNode(0, (string)$id))->setAttribute('id', $id),
            [new TextNode(0, str_pad('', 24))]
        );
    }

    /**
     * Add a delete mandate record to tree
     */
    public function addDeleteMandateRecord(string $payerNr)
    {
        $this->mandateRecords[] = new DeleteMandateRequestNode(
            0,
            $this->payeeBgNode,
            new PayerNumberNode(0, $payerNr),
            [new TextNode(0, str_pad('', 52))]
        );
    }

    /**
     * Add an accept digital mandate record to tree
     */
    public function addAcceptMandateRecord(string $payerNr)
    {
        $this->mandateRecords[] = new AcceptMandateRequestNode(
            0,
            $this->payeeBgNode,
            new PayerNumberNode(0, $payerNr),
            [new TextNode(0, str_pad('', 52))]
        );
    }

    /**
     * Add a reject digital mandate record to tree
     */
    public function addRejectMandateRecord(string $payerNr)
    {
        $this->mandateRecords[] = new RejectMandateRequestNode(
            0,
            $this->payeeBgNode,
            new PayerNumberNode(0, $payerNr),
            new TextNode(0, str_pad('', 48)),
            new TextNode(0, 'AV'),
            [new TextNode(0, '  ')]
        );
    }

    /**
     * Add an update mandate record to tree
     */
    public function addUpdateMandateRecord(string $payerNr, string $newPayerNr)
    {
        $this->mandateRecords[] = new UpdateMandateRequestNode(
            0,
            $this->payeeBgNode,
            new PayerNumberNode(0, $payerNr),
            $this->payeeBgNode,
            new PayerNumberNode(0, $newPayerNr),
            [new TextNode(0, str_pad('', 26))]
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
