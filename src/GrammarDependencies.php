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
 * Copyright 2016 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro;

use byrokrat\banking\AccountFactory;
use byrokrat\banking\BankgiroFactory;
use byrokrat\id\IdFactory;
use byrokrat\autogiro\Message\MessageFactory;

/**
 * Base class that handles dependencies form Grammar
 */
abstract class GrammarDependencies
{
    /**
     * @var AccountFactory
     */
    private $accountFactory;

    /**
     * @var BankgiroFactory
     */
    private $bankgiroFactory;

    /**
     * @var IdFactory
     */
    private $personalIdFactory;

    /**
     * @var IdFactory
     */
    private $organizationIdFactory;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var integer The current line number
     */
    protected $currentLineNr = 0;

    /**
     * Inject dependencies at construct
     */
    public function __construct(
        AccountFactory $accountFactory,
        BankgiroFactory $bankgiroFactory,
        IdFactory $personalIdFactory,
        IdFactory $organizationIdFactory,
        MessageFactory $messageFactory
    ) {
        $this->accountFactory = $accountFactory;
        $this->bankgiroFactory = $bankgiroFactory;
        $this->personalIdFactory = $personalIdFactory;
        $this->organizationIdFactory = $organizationIdFactory;
        $this->messageFactory = $messageFactory;
    }

    /**
     * Get line number of current parsed line
     */
    public function getCurrentLineCount(): int
    {
        return $this->currentLineNr;
    }

    /**
     * Reset line count
     *
     * @return void
     */
    public function resetLineCount()
    {
        $this->currentLineNr = 0;
    }

    /**
     * Get factory for regular account numbers
     */
    protected function getAccountFactory(): AccountFactory
    {
        return $this->accountFactory;
    }

    /**
     * Get factory for bankgiro account numbers only
     */
    protected function getBankgiroFactory(): BankgiroFactory
    {
        return $this->bankgiroFactory;
    }

    /**
     * Get factory for personal id numbers
     */
    protected function getPersonalIdFactory(): IdFactory
    {
        return $this->personalIdFactory;
    }

    /**
     * Get factory for organizational id numbers
     */
    protected function getOrganizationIdFactory(): IdFactory
    {
        return $this->organizationIdFactory;
    }

    /**
     * Get factory for BGC messages
     */
    protected function getMessageFactory(): MessageFactory
    {
        return $this->messageFactory;
    }
}
