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

namespace byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Tree\IdNode;
use byrokrat\id\OrganizationIdFactory;
use byrokrat\id\PersonalIdFactory;
use byrokrat\id\Exception as IdException;

/**
 * Validates the structure of id numbers in tree
 */
class IdVisitor extends ErrorAwareVisitor
{
    /**
     * @var OrganizationIdFactory
     */
    private $organizationIdFactory;

    /**
     * @var PersonalIdFactory
     */
    private $personalIdFactory;

    public function __construct(
        ErrorObject $errorObj,
        OrganizationIdFactory $organizationIdFactory,
        PersonalIdFactory $personalIdFactory
    ) {
        parent::__construct($errorObj);
        $this->organizationIdFactory = $organizationIdFactory;
        $this->personalIdFactory = $personalIdFactory;
    }

    public function beforeIdNode(IdNode $node): void
    {
        if ($node->hasAttribute('id')) {
            return;
        }

        try {
            if (in_array(substr($node->getValue(), 0, 2), ['00', '99'])) {
                $this->createOrganizationId($node);
                return;
            }

            $this->createPersonalId($node);
            return;
        } catch (IdException $exception) {
            $this->getErrorObject()->addError(
                "Invalid id number %s (%s) on line %s",
                $node->getValue(),
                $exception->getMessage(),
                (string)$node->getLineNr()
            );
        }
    }

    private function createOrganizationId(IdNode $node): void
    {
        $node->setAttribute(
            'id',
            $this->organizationIdFactory->createId(substr($node->getValue(), 2))
        );
    }

    private function createPersonalId(IdNode $node): void
    {
        $node->setAttribute(
            'id',
            $this->personalIdFactory->createId($node->getValue())
        );
    }
}
