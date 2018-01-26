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

namespace byrokrat\autogiro\Writer;

use byrokrat\autogiro\Layouts;
use byrokrat\autogiro\Tree\RecordNode;
use byrokrat\autogiro\Tree\Request\RequestOpening;
use byrokrat\autogiro\Tree\Request\AcceptDigitalMandateRequest;
use byrokrat\autogiro\Tree\Request\CreateMandateRequest;
use byrokrat\autogiro\Tree\Request\DeleteMandateRequest;
use byrokrat\autogiro\Tree\Request\RejectDigitalMandateRequest;
use byrokrat\autogiro\Tree\Request\UpdateMandateRequest;
use byrokrat\autogiro\Tree\Request\IncomingPaymentRequest;
use byrokrat\autogiro\Tree\Request\OutgoingPaymentRequest;
use byrokrat\autogiro\Tree\Request\MandateRequestSection;
use byrokrat\autogiro\Tree\Request\PaymentRequestSection;
use byrokrat\autogiro\Tree\Request\AmendmentRequestSection;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\ImmediateDateNode;
use byrokrat\autogiro\Tree\TextNode;
use byrokrat\autogiro\Tree\BgcNumberNode;
use byrokrat\autogiro\Tree\BankgiroNode;
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
     * Map section classes to record store array names
     */
    private const SECTION_TO_RECORD_STORE_MAP = [
        MandateRequestSection::CLASS => 'mandates',
        PaymentRequestSection::CLASS => 'payments',
        AmendmentRequestSection::CLASS => 'amendments'
    ];

    /**
     * @var RequestOpening Opening record used for each section
     */
    private $opening;

    /**
     * @var RecordNode[] List of created mandate requests
     */
    private $mandates;

    /**
     * @var RecordNode[] List of created payment requests
     */
    private $payments;

    /**
     * @var RecordNode[] List of created amendment requests
     */
    private $amendments;

    /**
     * @var string Payee BGC customer number
     */
    private $bgcNr;

    /**
     * @var BankgiroNode Wrapper around payee bankgiro account number
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
     * @param \DateTimeInterface   $date                 Creation date
     * @param IntervalFormatter    $intervalFormatter    Interval formatter
     * @param RepititionsFormatter $repititionsFormatter Repititions formatter
     */
    public function __construct(
        string $bgcNr,
        Bankgiro $bankgiro,
        \DateTimeInterface $date,
        IntervalFormatter $intervalFormatter,
        RepititionsFormatter $repititionsFormatter
    ) {
        $this->bgcNr = $bgcNr;
        $this->payeeBgNode = BankgiroNode::fromBankgiro($bankgiro);
        $this->date = $date;
        $this->intervalFormatter = $intervalFormatter;
        $this->repititionsFormatter = $repititionsFormatter;
        $this->reset();
    }

    /**
     * Reset builder to initial state
     */
    public function reset(): void
    {
        $this->opening = new RequestOpening(
            0,
            [
                'date' => DateNode::fromDate($this->date),
                'autogiro_txt' => new TextNode(0, 'AUTOGIRO'),
                'space' => new TextNode(0, str_pad('', 44)),
                'payee_bgc_number' => new BgcNumberNode(0, $this->bgcNr),
                'payee_bankgiro' => $this->payeeBgNode,
                'end' => new TextNode(0, '  ')
            ]
        );
        $this->mandates = [];
        $this->payments = [];
        $this->amendments = [];
    }

    /**
     * Add a new mandate request to tree
     */
    public function addCreateMandateRequest(string $payerNr, AccountNumber $account, IdInterface $id): void
    {
        $this->mandates[] = new CreateMandateRequest(
            0,
            [
                'payee_bankgiro' => $this->payeeBgNode,
                'payer_number' => new PayerNumberNode(0, $payerNr),
                'account' => AccountNode::fromAccount($account),
                'id' => IdNode::fromId($id),
                'end' => new TextNode(0, str_pad('', 24))
            ]
        );
    }

    /**
     * Add a delete mandate request to tree
     */
    public function addDeleteMandateRequest(string $payerNr): void
    {
        $this->mandates[] = new DeleteMandateRequest(
            0,
            [
                'payee_bankgiro' => $this->payeeBgNode,
                'payer_number' => new PayerNumberNode(0, $payerNr),
                'end' => new TextNode(0, str_pad('', 52))
            ]
        );
    }

    /**
     * Add an accept digital mandate request to tree
     */
    public function addAcceptDigitalMandateRequest(string $payerNr): void
    {
        $this->mandates[] = new AcceptDigitalMandateRequest(
            0,
            [
                'payee_bankgiro' => $this->payeeBgNode,
                'payer_number' => new PayerNumberNode(0, $payerNr),
                'end' => new TextNode(0, str_pad('', 52))
            ]
        );
    }

    /**
     * Add a reject digital mandate request to tree
     */
    public function addRejectDigitalMandateRequest(string $payerNr): void
    {
        $this->mandates[] = new RejectDigitalMandateRequest(
            0,
            [
                'payee_bankgiro' => $this->payeeBgNode,
                'payer_number' => new PayerNumberNode(0, $payerNr),
                'space' => new TextNode(0, str_pad('', 48)),
                'reject' => new TextNode(0, 'AV'),
                'end' => new TextNode(0, '  ')
            ]
        );
    }

    /**
     * Add an update mandate request to tree
     */
    public function addUpdateMandateRequest(string $payerNr, string $newPayerNr): void
    {
        $this->mandates[] = new UpdateMandateRequest(
            0,
            [
                'payee_bankgiro' => $this->payeeBgNode,
                'payer_number' => new PayerNumberNode(0, $payerNr),
                'new_payee_bankgiro' => $this->payeeBgNode,
                'new_payer_number' => new PayerNumberNode(0, $newPayerNr),
                'end' => new TextNode(0, str_pad('', 26))
            ]
        );
    }

    /**
     * Add an incoming payment request to tree
     */
    public function addIncomingPaymentRequest(
        string $payerNr,
        SEK $amount,
        \DateTimeInterface $date,
        string $ref,
        string $interval,
        int $repetitions
    ): void {
        $this->addPaymentRequest(
            IncomingPaymentRequest::CLASS,
            $payerNr,
            $amount,
            $date,
            $ref,
            $interval,
            $repetitions
        );
    }

    /**
     * Add an outgoing payment request to tree
     */
    public function addOutgoingPaymentRequest(
        string $payerNr,
        SEK $amount,
        \DateTimeInterface $date,
        string $ref,
        string $interval,
        int $repetitions
    ): void {
        $this->addPaymentRequest(
            OutgoingPaymentRequest::CLASS,
            $payerNr,
            $amount,
            $date,
            $ref,
            $interval,
            $repetitions
        );
    }

    /**
     * Add an incoming payment at next possible bank date request to tree
     */
    public function addImmediateIncomingPaymentRequest(string $payerNr, SEK $amount, string $ref): void
    {
        $this->addImmediatePaymentRequest(IncomingPaymentRequest::CLASS, $payerNr, $amount, $ref);
    }

    /**
     * Add an outgoing payment at next possible bank date request to tree
     */
    public function addImmediateOutgoingPaymentRequest(string $payerNr, SEK $amount, string $ref): void
    {
        $this->addImmediatePaymentRequest(OutgoingPaymentRequest::CLASS, $payerNr, $amount, $ref);
    }

    /**
     * Get the created request tree
     */
    public function buildTree(): FileNode
    {
        $sections = [];

        foreach (self::SECTION_TO_RECORD_STORE_MAP as $sectionClass => $recordStore) {
            if (!empty($this->$recordStore)) {
                $sections[] = new $sectionClass($this->opening, ...$this->$recordStore);
            }
        }

        return new FileNode(Layouts::LAYOUT_REQUEST, ...$sections);
    }

    private function addPaymentRequest(
        string $classname,
        string $payerNr,
        SEK $amount,
        \DateTimeInterface $date,
        string $ref,
        string $interval,
        int $repetitions
    ): void {
        $this->payments[] = new $classname(
            0,
            [
                'date' => DateNode::fromDate($date),
                'interval' => new IntervalNode(0, $this->intervalFormatter->format($interval)),
                'repetitions' => new RepetitionsNode(0, $this->repititionsFormatter->format($repetitions)),
                'space' => new TextNode(0, ' '),
                'payer_number' => new PayerNumberNode(0, $payerNr),
                'amount' => AmountNode::fromAmount($amount),
                'payee_bankgiro' => $this->payeeBgNode,
                'reference' => new TextNode(0, str_pad($ref, 16, ' ', STR_PAD_LEFT), '/^.{16}$/'),
                'end' => new TextNode(0, str_pad('', 11))
            ]
        );
    }

    private function addImmediatePaymentRequest(string $classname, string $payerNr, SEK $amount, string $ref): void
    {
        $this->payments[] = new $classname(
            0,
            [
                'date' => new ImmediateDateNode,
                'interval' => new IntervalNode(0, '0'),
                'repetitions' => new RepetitionsNode(0, '   '),
                'space' => new TextNode(0, ' '),
                'payer_number' => new PayerNumberNode(0, $payerNr),
                'amount' => AmountNode::fromAmount($amount),
                'payee_bankgiro' => $this->payeeBgNode,
                'reference' => new TextNode(0, str_pad($ref, 16, ' ', STR_PAD_LEFT), '/^.{16}$/'),
                'end' => new TextNode(0, str_pad('', 11))
            ]
        );
    }
}
