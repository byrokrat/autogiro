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
 * Copyright 2016-20 Hannes Forsgård
 */

declare(strict_types=1);

namespace byrokrat\autogiro\Writer;

use byrokrat\autogiro\Intervals;
use byrokrat\autogiro\Tree\AutogiroFile;
use byrokrat\autogiro\Tree\ImmediateDate;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\Number;
use byrokrat\autogiro\Tree\Obj;
use byrokrat\autogiro\Tree\Record;
use byrokrat\autogiro\Tree\Section;
use byrokrat\autogiro\Tree\Text;
use byrokrat\banking\AccountNumber;
use byrokrat\id\IdInterface;
use Money\Money;

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
     * @var Record Opening record used for each section
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
     * @var Obj Wrapper around payee bankgiro account number
     */
    private $payeeBgNode;

    /**
     * @var \DateTimeInterface Date of file creation
     */
    private $date;

    /**
     * @param string             $bgcNr    The BGC customer number of payee
     * @param AccountNumber      $bankgiro Payee bankgiro account number
     * @param \DateTimeInterface $date     Creation date
     */
    public function __construct(string $bgcNr, AccountNumber $bankgiro, \DateTimeInterface $date)
    {
        $this->bgcNr = $bgcNr;
        $this->payeeBgNode = new Obj(0, $bankgiro, 'PayeeBankgiro');
        $this->date = $date;
        $this->reset();
    }

    /**
     * Reset builder to initial state
     */
    public function reset(): void
    {
        $this->opening = new Record(
            'Opening',
            new Obj(0, $this->date, 'Date'),
            new Text(0, 'AUTOGIRO'),
            new Text(0, str_pad('', 44)),
            new Number(0, $this->bgcNr, 'PayeeBgcNumber'),
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
        $this->mandates[] = new Record(
            'CreateMandateRequest',
            $this->payeeBgNode,
            new Number(0, $payerNr, 'PayerNumber'),
            new Obj(0, $account, 'Account'),
            new Obj(0, $id, 'StateId'),
            new Text(0, str_pad('', 24))
        );
    }

    /**
     * Add a delete mandate request to tree
     */
    public function addDeleteMandateRequest(string $payerNr): void
    {
        $this->mandates[] = new Record(
            'DeleteMandateRequest',
            $this->payeeBgNode,
            new Number(0, $payerNr, 'PayerNumber'),
            new Text(0, str_pad('', 52))
        );
    }

    /**
     * Add an accept digital mandate request to tree
     */
    public function addAcceptDigitalMandateRequest(string $payerNr): void
    {
        $this->mandates[] = new Record(
            'AcceptDigitalMandateRequest',
            $this->payeeBgNode,
            new Number(0, $payerNr, 'PayerNumber'),
            new Text(0, str_pad('', 52))
        );
    }

    /**
     * Add a reject digital mandate request to tree
     */
    public function addRejectDigitalMandateRequest(string $payerNr): void
    {
        $this->mandates[] = new Record(
            'RejectDigitalMandateRequest',
            $this->payeeBgNode,
            new Number(0, $payerNr, 'PayerNumber'),
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
        $this->mandates[] = new Record(
            'UpdateMandateRequest',
            $this->payeeBgNode,
            new Number(0, $payerNr, 'PayerNumber'),
            $this->payeeBgNode,
            new Number(0, $newPayerNr, 'PayerNumber'),
            new Text(0, str_pad('', 26))
        );
    }

    /**
     * Add an incoming payment request to tree
     */
    public function addIncomingPaymentRequest(
        string $payerNr,
        Money $amount,
        \DateTimeInterface $date,
        string $ref,
        string $interval,
        int $repetitions
    ): void {
        $this->addPaymentRequest(
            'IncomingPaymentRequest',
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
        Money $amount,
        \DateTimeInterface $date,
        string $ref,
        string $interval,
        int $repetitions
    ): void {
        $this->addPaymentRequest(
            'OutgoingPaymentRequest',
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
    public function addImmediateIncomingPaymentRequest(string $payerNr, Money $amount, string $ref): void
    {
        $this->addImmediatePaymentRequest('IncomingPaymentRequest', $payerNr, $amount, $ref);
    }

    /**
     * Add an outgoing payment at next possible bank date request to tree
     */
    public function addImmediateOutgoingPaymentRequest(string $payerNr, Money $amount, string $ref): void
    {
        $this->addImmediatePaymentRequest('OutgoingPaymentRequest', $payerNr, $amount, $ref);
    }

    /**
     * Add a delete payment request to tree
     */
    public function addDeletePaymentRequest(string $payerNr): void
    {
        $this->amendments[] = new Record(
            'AmendmentRequest',
            $this->payeeBgNode,
            new Number(0, $payerNr, 'PayerNumber'),
            new Text(0, str_repeat(' ', 52))
        );
    }

    /**
     * Get the created request tree
     */
    public function buildTree(): Node
    {
        $sections = [];

        foreach (self::SECTION_TO_RECORD_STORE_MAP as $sectionName => $recordStore) {
            if (!empty($this->$recordStore)) {
                $sections[] = new Section($sectionName, $this->opening, ...$this->$recordStore);
            }
        }

        return new AutogiroFile('AutogiroRequestFile', ...$sections);
    }

    private function addPaymentRequest(
        string $nodename,
        string $payerNr,
        Money $amount,
        \DateTimeInterface $date,
        string $ref,
        string $interval,
        int $repetitions
    ): void {
        $this->payments[] = new Record(
            $nodename,
            new Obj(0, $date, 'Date'),
            new Number(0, $interval, 'Interval'),
            new Number(0, (string)$repetitions, 'Repetitions'),
            new Text(0, ' '),
            new Number(0, $payerNr, 'PayerNumber'),
            new Obj(0, $amount, 'Amount'),
            $this->payeeBgNode,
            new Text(0, str_pad($ref, 16, ' ', STR_PAD_LEFT)),
            new Text(0, str_pad('', 11))
        );
    }

    private function addImmediatePaymentRequest(string $nodename, string $payerNr, Money $amount, string $ref): void
    {
        $this->payments[] = new Record(
            $nodename,
            new ImmediateDate(),
            new Number(0, Intervals::INTERVAL_ONCE, 'Interval'),
            new Number(0, '0', 'Repetitions'),
            new Text(0, ' '),
            new Number(0, $payerNr, 'PayerNumber'),
            new Obj(0, $amount, 'Amount'),
            $this->payeeBgNode,
            new Text(0, str_pad($ref, 16, ' ', STR_PAD_LEFT)),
            new Text(0, str_pad('', 11))
        );
    }
}
