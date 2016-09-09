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

namespace byrokrat\autogiro\Tree;

use byrokrat\banking\Bankgiro;

/**
 * Generic opening record value object
 */
class OpeningNode implements NodeInterface
{
    use Attr\BankgiroAttribute, Attr\DateAttribute, Attr\LineNrAttribute;

    /**
     * @var string Name of layout file belongs to
     */
    private $layoutName;

    /**
     * @var string BGC customer number
     */
    private $customerNr;

    /**
     * Load values at construct
     */
    public function __construct(string $layoutName, \DateTimeInterface $date, string $customerNr, Bankgiro $bankgiro, int $lineNr)
    {
        $this->layoutName = $layoutName;
        $this->date = $date;
        $this->customerNr = $customerNr;
        $this->bankgiro = $bankgiro;
        $this->lineNr = $lineNr;
    }

    /**
     * Get name of layout file belongs to
     */
    public function getLayoutId(): string
    {
        return $this->layoutName;
    }

    /**
     * Get BGC customer number
     */
    public function getCustomerNumber(): string
    {
        return $this->customerNr;
    }

    /**
     * Accept a visitor
     */
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visitOpeningNode($this);
    }
}
