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

namespace byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\Obj;
use Money\Money;
use Money\MoneyParser;

/**
 * Create amount object under child 'Object'
 */
final class AmountVisitor extends Visitor
{
    use ErrorAwareTrait;

    /**
     * @var MoneyParser
     */
    private $moneyParser;

    public function __construct(ErrorObject $errorObj, MoneyParser $moneyParser)
    {
        $this->setErrorObject($errorObj);
        $this->moneyParser = $moneyParser;
    }

    public function beforeAmount(Node $node): void
    {
        if ($node->hasChild('Object')) {
            return;
        }

        $signalStr = (string)$node->getValueFrom('Text');

        if (trim($signalStr) == '') {
            return;
        }

        try {
            $node->addChild(
                new Obj(
                    $node->getLineNr(),
                    $this->moneyParser->parse($signalStr)
                )
            );
        } catch (\Exception $e) {
            $this->getErrorObject()->addError(
                "Invalid signaled amount %s on line %s",
                (string)$node->getValueFrom('Text'),
                (string)$node->getLineNr()
            );
        }
    }
}
