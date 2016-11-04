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

namespace byrokrat\autogiro\Processor;

use byrokrat\autogiro\Tree\OrganizationIdNode;
use byrokrat\autogiro\Tree\PersonalIdNode;
use byrokrat\id\OrganizationIdFactory;
use byrokrat\id\PersonalIdFactory;
use byrokrat\id\Exception as IdException;

/**
 * Validates the structure of id numbers in tree
 */
class IdProcessor extends Processor
{
    /**
     * @var OrganizationIdFactory
     */
    private $organizationIdFactory;

    /**
     * @var PersonalIdFactory
     */
    private $personalIdFactory;

    public function __construct(OrganizationIdFactory $organizationIdFactory, PersonalIdFactory $personalIdFactory)
    {
        $this->organizationIdFactory = $organizationIdFactory;
        $this->personalIdFactory = $personalIdFactory;
    }

    public function visitOrganizationIdNode(OrganizationIdNode $node)
    {
        try {
            $node->setAttribute(
                'id',
                $this->organizationIdFactory->create($node->getValue())
            );
        } catch (IdException $e) {
            $this->addError(
                "Invalid organizational id number %s on line %s",
                $node->getValue(),
                (string)$node->getLineNr()
            );
        }
    }

    public function visitPersonalIdNode(PersonalIdNode $node)
    {
        try {
            $node->setAttribute(
                'id',
                $this->personalIdFactory->create($node->getValue())
            );
        } catch (IdException $e) {
            $this->addError(
                "Invalid personal id number %s on line %s",
                $node->getValue(),
                (string)$node->getLineNr()
            );
        }
    }
}
