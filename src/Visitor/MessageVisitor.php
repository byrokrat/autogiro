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
 * Copyright 2016-17 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Tree\MessageNode;
use byrokrat\autogiro\Tree\IntervalNode;
use byrokrat\autogiro\Messages;
use byrokrat\autogiro\Intervals;

/**
 * Visitor of message nodes in tree
 */
class MessageVisitor extends ErrorAwareVisitor
{
    public function beforeMessageNode(MessageNode $node)
    {
        $this->setMessageAttr($node, Messages::MESSAGE_MAP);
    }

    public function beforeIntervalNode(IntervalNode $node)
    {
        $this->setMessageAttr($node, Intervals::MESSAGE_MAP);
    }

    private function setMessageAttr(MessageNode $node, array $messageMap)
    {
        if (!isset($messageMap[$node->getValue()])) {
            return $this->getErrorObject()->addError(
                "Invalid message id %s on line %s",
                $node->getValue(),
                (string)$node->getLineNr()
            );
        }

        $node->setAttribute(
            'message',
            $messageMap[$node->getValue()]
        );
    }
}
