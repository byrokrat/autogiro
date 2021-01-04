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

namespace byrokrat\autogiro\Writer;

use byrokrat\autogiro\Intervals;
use byrokrat\banking\AccountNumber;
use byrokrat\id\IdInterface;
use Money\Money;

interface WriterInterface
{
    /**
     * Build and return request content
     */
    public function getContent(): string;

    /**
     * Reset internal build queue
     */
    public function reset(): void;

    /**
     * Add a new mandate request to the build queue
     */
    public function addNewMandate(string $payerNr, AccountNumber $account, IdInterface $id): void;

    /**
     * Add a delete mandate request to the build queue
     */
    public function deleteMandate(string $payerNr): void;

    /**
     * Add an accept digital mandate request to the build queue
     */
    public function acceptDigitalMandate(string $payerNr): void;

    /**
     * Add a reject digital mandate request to the build queue
     */
    public function rejectDigitalMandate(string $payerNr): void;

    /**
     * Add an update mandate request to the build queue
     */
    public function updateMandate(string $payerNr, string $newPayerNr): void;

    /**
     * Add an incoming payment request to the build queue
     *
     * @param string             $payerNr     Number identifying the payer
     * @param Money              $amount      The requested payment amount
     * @param \DateTimeInterface $date        Requested date of payment (or first date for repeated payments)
     * @param string             $ref         Custom payment reference number
     * @param string             $interval    Interval for repeted payment, use one of the Intervals constants
     * @param integer            $repetitions Number of repititions (0 repeates payments indefinitely)
     */
    public function addPayment(
        string $payerNr,
        Money $amount,
        \DateTimeInterface $date,
        string $ref = '',
        string $interval = Intervals::INTERVAL_ONCE,
        int $repetitions = 0
    ): void;

    /**
     * Add an incoming payment request to the build queue
     *
     * @param string             $payerNr Number identifying the payer
     * @param Money              $amount  The requested payment amount
     * @param \DateTimeInterface $date    Requested  first date of payment
     * @param string             $ref     Custom payment reference number
     */
    public function addMonthlyPayment(
        string $payerNr,
        Money $amount,
        \DateTimeInterface $date,
        string $ref = ''
    ): void;

    /**
     * Add an incoming payment at next possible bank date request to the build queue
     *
     * @param string $payerNr Number identifying the payer
     * @param Money  $amount  The requested payment amount
     * @param string $ref     Custom payment reference number
     */
    public function addImmediatePayment(string $payerNr, Money $amount, string $ref = ''): void;

    /**
     * Add an outgoing payment request to the build queue
     *
     * @param string             $payerNr     Number identifying the payer
     * @param Money              $amount      The requested payment amount
     * @param \DateTimeInterface $date        Requested date of payment (or first date for repeated payments)
     * @param string             $ref         Custom payment reference number
     * @param string             $interval    Interval for repeted payment, use one of the Intervals constants
     * @param integer            $repetitions Number of repititions (0 repeateds payments indefinitely)
     */
    public function addOutgoingPayment(
        string $payerNr,
        Money $amount,
        \DateTimeInterface $date,
        string $ref = '',
        string $interval = Intervals::INTERVAL_ONCE,
        int $repetitions = 0
    ): void;

    /**
     * Add an outgoing payment on next possible bank date request to the build queue
     *
     * @param string $payerNr Number identifying the payer
     * @param Money  $amount  The requested payment amount
     * @param string $ref     Custom payment reference number
     */
    public function addImmediateOutgoingPayment(string $payerNr, Money $amount, string $ref = ''): void;

    /**
     * Delete all payments to or from payer
     */
    public function deletePayments(string $payerNr): void;
}
