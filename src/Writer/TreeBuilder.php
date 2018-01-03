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
use byrokrat\autogiro\Tree\Record\Request\AcceptDigitalMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\CreateMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\DeleteMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\RejectDigitalMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\UpdateMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\IncomingTransactionRequestNode;
use byrokrat\autogiro\Tree\Record\Request\OutgoingTransactionRequestNode;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\ImmediateDateNode;
use byrokrat\autogiro\Tree\TextNode;
use byrokrat\autogiro\Tree\PayeeBgcNumberNode;
use byrokrat\autogiro\Tree\PayeeBankgiroNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\AccountNode;
use byrokrat\autogiro\Tree\IdNode;
use byrokrat\autogiro\Tree\IntervalNode;
use byrokrat\autogiro\Tree\RepetitionsNode;
use byrokrat\autogiro\Tree\AmountNode;
use byrokrat\banking\AccountNumber;
use byrokrat\banking\Bankgiro;
use byrokrat\id\IdInterface;
use byrokrat\amount\Currency\SEK;

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
     * @var IntervalFormatter
     */
    private $intervalFormatter;

    /**
     * @var RepititionsFormatter
     */
    private $repititionsFormatter;

    /**
     * @param string               $bgcNr                The BGC customer number of payee
     * @param Bankgiro             $bankgiro             Payee bankgiro account number
     * @param \DateTimeInterface   $date                 Optional creation date
     * @param IntervalFormatter    $intervalFormatter    Optional interval formatter
     * @param RepititionsFormatter $repititionsFormatter Optional repititions formatter
     */
    public function __construct(
        string $bgcNr,
        Bankgiro $bankgiro,
        \DateTimeInterface $date = null,
        IntervalFormatter $intervalFormatter = null,
        RepititionsFormatter $repititionsFormatter = null
    ) {
        $this->bgcNr = $bgcNr;
        $this->payeeBgNode = (new PayeeBankgiroNode(0, $bankgiro->getNumber()))->setAttribute('account', $bankgiro);
        $this->date = $date ?: new \DateTimeImmutable;
        $this->intervalFormatter = $intervalFormatter ?: new IntervalFormatter;
        $this->repititionsFormatter = $repititionsFormatter ?: new RepititionsFormatter;
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
    public function addCreateMandateRecord(string $payerNr, AccountNumber $account, IdInterface $id)
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
    public function addAcceptDigitalMandateRecord(string $payerNr)
    {
        $this->mandateRecords[] = new AcceptDigitalMandateRequestNode(
            0,
            $this->payeeBgNode,
            new PayerNumberNode(0, $payerNr),
            [new TextNode(0, str_pad('', 52))]
        );
    }

    /**
     * Add a reject digital mandate record to tree
     */
    public function addRejectDigitalMandateRecord(string $payerNr)
    {
        $this->mandateRecords[] = new RejectDigitalMandateRequestNode(
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
     * Add an incoming transaction record to tree
     */
    public function addIncomingTransactionRecord(
        string $payerNr,
        SEK $amount,
        \DateTimeInterface $date,
        string $ref,
        string $interval,
        int $repetitions
    ) {
        $this->addTransactionRecord(
            IncomingTransactionRequestNode::CLASS,
            $payerNr,
            $amount,
            $date,
            $ref,
            $interval,
            $repetitions
        );
    }

    /**
     * Add an outgoing transaction record to tree
     */
    public function addOutgoingTransactionRecord(
        string $payerNr,
        SEK $amount,
        \DateTimeInterface $date,
        string $ref,
        string $interval,
        int $repetitions
    ) {
        $this->addTransactionRecord(
            OutgoingTransactionRequestNode::CLASS,
            $payerNr,
            $amount,
            $date,
            $ref,
            $interval,
            $repetitions
        );
    }

    /**
     * Add an incoming transaction at next possible bank date record to tree
     */
    public function addImmediateIncomingTransactionRecord(string $payerNr, SEK $amount, string $ref)
    {
        $this->addImmediateTransactionRecord(IncomingTransactionRequestNode::CLASS, $payerNr, $amount, $ref);
    }

    /**
     * Add an outgoing transaction at next possible bank date record to tree
     */
    public function addImmediateOutgoingTransactionRecord(string $payerNr, SEK $amount, string $ref)
    {
        $this->addImmediateTransactionRecord(OutgoingTransactionRequestNode::CLASS, $payerNr, $amount, $ref);
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

    private function addTransactionRecord(
        string $classname,
        string $payerNr,
        SEK $amount,
        \DateTimeInterface $date,
        string $ref,
        string $interval,
        int $repetitions
    ) {
        $this->transactionRecords[] = new $classname(
            0,
            (new DateNode(0, $date->format('Ymd')))->setAttribute('date', $date),
            new IntervalNode(0, $this->intervalFormatter->format($interval)),
            new RepetitionsNode(0, $this->repititionsFormatter->format($repetitions)),
            new TextNode(0, ' '),
            new PayerNumberNode(0, $payerNr),
            (new AmountNode(0, $amount->getSignalString()))->setAttribute('amount', $amount),
            $this->payeeBgNode,
            new TextNode(0, str_pad($ref, 16, ' ', STR_PAD_LEFT), '/^.{16}$/'),
            [new TextNode(0, str_pad('', 11))]
        );
    }

    private function addImmediateTransactionRecord(string $classname, string $payerNr, SEK $amount, string $ref)
    {
        $this->transactionRecords[] = new $classname(
            0,
            new ImmediateDateNode,
            new IntervalNode(0, '0'),
            new RepetitionsNode(0, '   '),
            new TextNode(0, ' '),
            new PayerNumberNode(0, $payerNr),
            (new AmountNode(0, $amount->getSignalString()))->setAttribute('amount', $amount),
            $this->payeeBgNode,
            new TextNode(0, str_pad($ref, 16, ' ', STR_PAD_LEFT), '/^.{16}$/'),
            [new TextNode(0, str_pad('', 11))]
        );
    }
}
