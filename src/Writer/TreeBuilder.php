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
use byrokrat\autogiro\Tree\Record;
use byrokrat\autogiro\Tree\Request\RequestOpening;
use byrokrat\autogiro\Tree\Request\AcceptDigitalMandateRequest;
use byrokrat\autogiro\Tree\Request\CreateMandateRequest;
use byrokrat\autogiro\Tree\Request\DeleteMandateRequest;
use byrokrat\autogiro\Tree\Request\RejectDigitalMandateRequest;
use byrokrat\autogiro\Tree\Request\UpdateMandateRequest;
use byrokrat\autogiro\Tree\Request\IncomingPaymentRequest;
use byrokrat\autogiro\Tree\Request\OutgoingPaymentRequest;
use byrokrat\autogiro\Tree\Account;
use byrokrat\autogiro\Tree\Amount;
use byrokrat\autogiro\Tree\AutogiroFile;
use byrokrat\autogiro\Tree\Date;
use byrokrat\autogiro\Tree\ImmediateDate;
use byrokrat\autogiro\Tree\Interval;
use byrokrat\autogiro\Tree\PayeeBgcNumber;
use byrokrat\autogiro\Tree\PayeeBankgiro;
use byrokrat\autogiro\Tree\PayerNumber;
use byrokrat\autogiro\Tree\Repetitions;
use byrokrat\autogiro\Tree\Section;
use byrokrat\autogiro\Tree\StateId;
use byrokrat\autogiro\Tree\Text;
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
        'MandateRequestSection' => 'mandates',
        'PaymentRequestSection' => 'payments',
        'AmendmentRequestSection' => 'amendments'
    ];

    /**
     * @var RequestOpening Opening record used for each section
     */
    private $opening;

    /**
     * @var Record[] List of created mandate requests
     */
    private $mandates;

    /**
     * @var Record[] List of created payment requests
     */
    private $payments;

    /**
     * @var Record[] List of created amendment requests
     */
    private $amendments;

    /**
     * @var string Payee BGC customer number
     */
    private $bgcNr;

    /**
     * @var PayeeBankgiro Wrapper around payee bankgiro account number
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
        $this->payeeBgNode = PayeeBankgiro::fromBankgiro($bankgiro);
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
            Date::fromDate($this->date),
            new Text(0, 'AUTOGIRO'),
            new Text(0, str_pad('', 44)),
            new PayeeBgcNumber(0, $this->bgcNr),
            $this->payeeBgNode,
            new Text(0, '  ')
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
            $this->payeeBgNode,
            new PayerNumber(0, $payerNr),
            Account::fromAccount($account),
            StateId::fromId($id),
            new Text(0, str_pad('', 24))
        );
    }

    /**
     * Add a delete mandate request to tree
     */
    public function addDeleteMandateRequest(string $payerNr): void
    {
        $this->mandates[] = new DeleteMandateRequest(
            0,
            $this->payeeBgNode,
            new PayerNumber(0, $payerNr),
            new Text(0, str_pad('', 52))
        );
    }

    /**
     * Add an accept digital mandate request to tree
     */
    public function addAcceptDigitalMandateRequest(string $payerNr): void
    {
        $this->mandates[] = new AcceptDigitalMandateRequest(
            0,
            $this->payeeBgNode,
            new PayerNumber(0, $payerNr),
            new Text(0, str_pad('', 52))
        );
    }

    /**
     * Add a reject digital mandate request to tree
     */
    public function addRejectDigitalMandateRequest(string $payerNr): void
    {
        $this->mandates[] = new RejectDigitalMandateRequest(
            0,
            $this->payeeBgNode,
            new PayerNumber(0, $payerNr),
            new Text(0, str_pad('', 48)),
            new Text(0, 'AV'),
            new Text(0, '  ')
        );
    }

    /**
     * Add an update mandate request to tree
     */
    public function addUpdateMandateRequest(string $payerNr, string $newPayerNr): void
    {
        $this->mandates[] = new UpdateMandateRequest(
            0,
            $this->payeeBgNode,
            new PayerNumber(0, $payerNr),
            $this->payeeBgNode,
            new PayerNumber(0, $newPayerNr),
            new Text(0, str_pad('', 26))
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
    public function buildTree(): AutogiroFile
    {
        $sections = [];

        foreach (self::SECTION_TO_RECORD_STORE_MAP as $sectionName => $recordStore) {
            if (!empty($this->$recordStore)) {
                $sections[] = new Section($sectionName, $this->opening, ...$this->$recordStore);
            }
        }

        return new AutogiroFile(Layouts::LAYOUT_REQUEST, ...$sections);
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
            Date::fromDate($date),
            new Interval(0, $this->intervalFormatter->format($interval)),
            new Repetitions(0, $this->repititionsFormatter->format($repetitions)),
            new Text(0, ' '),
            new PayerNumber(0, $payerNr),
            Amount::fromAmount($amount),
            $this->payeeBgNode,
            new Text(0, str_pad($ref, 16, ' ', STR_PAD_LEFT), '/^.{16}$/'),
            new Text(0, str_pad('', 11))
        );
    }

    private function addImmediatePaymentRequest(string $classname, string $payerNr, SEK $amount, string $ref): void
    {
        $this->payments[] = new $classname(
            0,
            new ImmediateDate,
            new Interval(0, '0'),
            new Repetitions(0, '   '),
            new Text(0, ' '),
            new PayerNumber(0, $payerNr),
            Amount::fromAmount($amount),
            $this->payeeBgNode,
            new Text(0, str_pad($ref, 16, ' ', STR_PAD_LEFT), '/^.{16}$/'),
            new Text(0, str_pad('', 11))
        );
    }
}
