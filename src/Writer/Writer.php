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
 * Copyright 2016-21 Hannes Forsgård
 */

declare(strict_types=1);

namespace byrokrat\autogiro\Writer;

use byrokrat\autogiro\Intervals;
use byrokrat\banking\AccountNumber;
use byrokrat\id\IdInterface;
use Money\Money;

final class Writer implements WriterInterface
{
    /**
     * @var TreeBuilder Helper used when building trees
     */
    private $treeBuilder;

    /**
     * @var PrintingVisitor Helper used when generating content
     */
    private $printer;

    public function __construct(TreeBuilder $treeBuilder, PrintingVisitor $printer)
    {
        $this->treeBuilder = $treeBuilder;
        $this->printer = $printer;
    }

    public function getContent(): string
    {
        $tree = $this->treeBuilder->buildTree();
        $output = new Output();
        $this->printer->setOutput($output);
        $tree->accept($this->printer);

        return rtrim($output->getContent(), "\r\n");
    }

    public function reset(): void
    {
        $this->treeBuilder->reset();
    }

    public function addNewMandate(string $payerNr, AccountNumber $account, IdInterface $id): void
    {
        $this->treeBuilder->addCreateMandateRequest($payerNr, $account, $id);
    }

    public function deleteMandate(string $payerNr): void
    {
        $this->treeBuilder->addDeleteMandateRequest($payerNr);
    }

    public function acceptDigitalMandate(string $payerNr): void
    {
        $this->treeBuilder->addAcceptDigitalMandateRequest($payerNr);
    }

    public function rejectDigitalMandate(string $payerNr): void
    {
        $this->treeBuilder->addRejectDigitalMandateRequest($payerNr);
    }

    public function updateMandate(string $payerNr, string $newPayerNr): void
    {
        $this->treeBuilder->addUpdateMandateRequest($payerNr, $newPayerNr);
    }

    public function addPayment(
        string $payerNr,
        Money $amount,
        \DateTimeInterface $date,
        string $ref = '',
        string $interval = Intervals::INTERVAL_ONCE,
        int $repetitions = 0
    ): void {
        $this->treeBuilder->addIncomingPaymentRequest($payerNr, $amount, $date, $ref, $interval, $repetitions);
    }

    public function addMonthlyPayment(string $payerNr, Money $amount, \DateTimeInterface $date, string $ref = ''): void
    {
        $this->addPayment($payerNr, $amount, $date, $ref, Intervals::INTERVAL_MONTHLY_ON_DATE, 0);
    }

    public function addImmediatePayment(string $payerNr, Money $amount, string $ref = ''): void
    {
        $this->treeBuilder->addImmediateIncomingPaymentRequest($payerNr, $amount, $ref);
    }

    public function addOutgoingPayment(
        string $payerNr,
        Money $amount,
        \DateTimeInterface $date,
        string $ref = '',
        string $interval = Intervals::INTERVAL_ONCE,
        int $repetitions = 0
    ): void {
        $this->treeBuilder->addOutgoingPaymentRequest($payerNr, $amount, $date, $ref, $interval, $repetitions);
    }

    public function addImmediateOutgoingPayment(string $payerNr, Money $amount, string $ref = ''): void
    {
        $this->treeBuilder->addImmediateOutgoingPaymentRequest($payerNr, $amount, $ref);
    }

    public function deletePayments(string $payerNr): void
    {
        $this->treeBuilder->addDeletePaymentRequest($payerNr);
    }
}
