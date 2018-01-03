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

use byrokrat\autogiro\Intervals;
use byrokrat\autogiro\Visitor\Visitor;
use byrokrat\banking\AccountNumber;
use byrokrat\id\IdInterface;
use byrokrat\amount\Currency\SEK;

/**
 * Facade for creating autogiro request files
 */
class Writer
{
    /**
     * @var TreeBuilder Helper used when building trees
     */
    private $treeBuilder;

    /**
     * @var PrintingVisitor Helper used when generating content
     */
    private $printer;

    /**
     * @var Visitor Helper used to validate and process tree
     */
    private $visitor;

    public function __construct(TreeBuilder $treeBuilder, PrintingVisitor $printer, Visitor $visitor)
    {
        $this->treeBuilder = $treeBuilder;
        $this->printer = $printer;
        $this->visitor = $visitor;
    }

    /**
     * Build and return request content
     */
    public function getContent(): string
    {
        $tree = $this->treeBuilder->buildTree();
        $tree->accept($this->visitor);
        $output = new Output;
        $this->printer->setOutput($output);
        $tree->accept($this->printer);

        return rtrim($output->getContent(), "\r\n");
    }

    /**
     * Reset internal build queue
     */
    public function reset()
    {
        $this->treeBuilder->reset();
    }

    /**
     * Add a new mandate request to the build queue
     *
     * @param string        $payerNr Number identifying the payer
     * @param AccountNumber $account Payer account number
     * @param IdInterface   $id      Payer id number
     */
    public function addNewMandate(string $payerNr, AccountNumber $account, IdInterface $id)
    {
        $this->treeBuilder->addCreateMandateRecord($payerNr, $account, $id);
    }

    /**
     * Add a delete mandate request to the build queue
     *
     * @param string $payerNr Number identifying the payer
     */
    public function deleteMandate(string $payerNr)
    {
        $this->treeBuilder->addDeleteMandateRecord($payerNr);
    }

    /**
     * Add an accept digital mandate request to the build queue
     *
     * @param string $payerNr Number identifying the payer
     */
    public function acceptDigitalMandate(string $payerNr)
    {
        $this->treeBuilder->addAcceptDigitalMandateRecord($payerNr);
    }

    /**
     * Add a reject digital mandate request to the build queue
     *
     * @param string $payerNr Number identifying the payer
     */
    public function rejectDigitalMandate(string $payerNr)
    {
        $this->treeBuilder->addRejectDigitalMandateRecord($payerNr);
    }

    /**
     * Add an update mandate request to the build queue
     *
     * @param string $payerNr    Old number identifying the payer
     * @param string $newPayerNr New number identifying the payer
     */
    public function updateMandate(string $payerNr, string $newPayerNr)
    {
        $this->treeBuilder->addUpdateMandateRecord($payerNr, $newPayerNr);
    }

    /**
     * Add an incoming transaction request to the build queue
     *
     * @param string             $payerNr     Number identifying the payer
     * @param SEK                $amount      The requested transaction amount
     * @param \DateTimeInterface $date        Requested date of transaction (or first date for repeated transactions)
     * @param string             $ref         Custom transaction reference number
     * @param string             $interval    Interval for repeted transaction, use one of the Intervals constants
     * @param integer            $repetitions Number of repititions (0 repeates transactions indefinitely)
     */
    public function addTransaction(
        string $payerNr,
        SEK $amount,
        \DateTimeInterface $date,
        string $ref = '',
        string $interval = Intervals::INTERVAL_ONCE,
        int $repetitions = 0
    ) {
        $this->treeBuilder->addIncomingTransactionRecord($payerNr, $amount, $date, $ref, $interval, $repetitions);
    }

    /**
     * Add an incoming transaction request to the build queue
     *
     * @param string             $payerNr     Number identifying the payer
     * @param SEK                $amount      The requested transaction amount
     * @param \DateTimeInterface $date        Requested  first date of transaction
     * @param string             $ref         Custom transaction reference number
     */
    public function addMonthlyTransaction(string $payerNr, SEK $amount, \DateTimeInterface $date, string $ref = '')
    {
        $this->addTransaction($payerNr, $amount, $date, $ref, Intervals::INTERVAL_MONTHLY_ON_DATE, 0);
    }

    /**
     * Add an incoming transaction at next possible bank date request to the build queue
     *
     * @param string $payerNr Number identifying the payer
     * @param SEK    $amount  The requested transaction amount
     * @param string $ref     Custom transaction reference number
     */
    public function addImmediateTransaction(string $payerNr, SEK $amount, string $ref = '')
    {
        $this->treeBuilder->addImmediateIncomingTransactionRecord($payerNr, $amount, $ref);
    }

    /**
     * Add an outgoing transaction request to the build queue
     *
     * @param string             $payerNr     Number identifying the payer
     * @param SEK                $amount      The requested transaction amount
     * @param \DateTimeInterface $date        Requested date of transaction (or first date for repeated transactions)
     * @param string             $ref         Custom transaction reference number
     * @param string             $interval    Interval for repeted transaction, use one of the Intervals constants
     * @param integer            $repetitions Number of repititions (0 repeateds transactions indefinitely)
     */
    public function addOutgoingTransaction(
        string $payerNr,
        SEK $amount,
        \DateTimeInterface $date,
        string $ref = '',
        string $interval = Intervals::INTERVAL_ONCE,
        int $repetitions = 1
    ) {
        $this->treeBuilder->addOutgoingTransactionRecord($payerNr, $amount, $date, $ref, $interval, $repetitions);
    }

    /**
     * Add an outgoing transaction on next possible bank date request to the build queue
     *
     * @param string $payerNr Number identifying the payer
     * @param SEK    $amount  The requested transaction amount
     * @param string $ref     Custom transaction reference number
     */
    public function addImmediateOutgoingTransaction(string $payerNr, SEK $amount, string $ref = '')
    {
        $this->treeBuilder->addImmediateOutgoingTransactionRecord($payerNr, $amount, $ref);
    }
}
