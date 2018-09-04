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

use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\Obj;
use byrokrat\id\IdFactoryInterface;
use byrokrat\id\Exception as IdException;

/**
 * Validates the structure of id numbers in tree
 *
 * Creates Id object as child 'Object'
 */
class StateIdVisitor extends ErrorAwareVisitor
{
    /**
     * @var IdFactoryInterface
     */
    private $organizationIdFactory;

    /**
     * @var IdFactoryInterface
     */
    private $personalIdFactory;

    public function __construct(
        ErrorObject $errorObj,
        IdFactoryInterface $organizationIdFactory,
        IdFactoryInterface $personalIdFactory
    ) {
        parent::__construct($errorObj);
        $this->organizationIdFactory = $organizationIdFactory;
        $this->personalIdFactory = $personalIdFactory;
    }

    public function beforeStateId(Node $node): void
    {
        if ($node->hasChild('Object')) {
            return;
        }

        $number = (string)$node->getValueFrom('Number');

        if (!trim($number, '0')) {
            return;
        }

        try {
            $id = in_array(substr($number, 0, 2), ['00', '99'])
                ? $this->organizationIdFactory->createId(substr($number, 2))
                : $this->personalIdFactory->createId($number);

            $node->addChild(new Obj($node->getLineNr(), $id));
        } catch (IdException $exception) {
            $this->getErrorObject()->addError(
                "Invalid id number %s (%s) on line %s",
                $number,
                $exception->getMessage(),
                (string)$node->getLineNr()
            );
        }
    }
}
