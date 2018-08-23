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

use byrokrat\autogiro\Tree\AutogiroFile;
use byrokrat\autogiro\Tree\Message;
use byrokrat\autogiro\Tree\Interval;
use byrokrat\autogiro\Messages;
use byrokrat\autogiro\Intervals;

/**
 * Visitor of message nodes in tree
 *
 * Creates string message as attribute 'message'
 */
class MessageVisitor extends ErrorAwareVisitor
{
    /**
     * @var string
     */
    private $layout;

    public function beforeAutogiroFile(AutogiroFile $node): void
    {
        $this->layout = $node->getAttribute('layout');
    }

    public function beforeMessage(Message $node): void
    {
        if ($node->hasAttribute('message')) {
            return;
        }

        $messageId = $node->hasAttribute('message_id')
            ? $node->getAttribute('message_id')
            : $this->layout . '.' . $node->getValue();

        if (!isset(Messages::MESSAGE_MAP[$messageId])) {
            $this->getErrorObject()->addError(
                "Invalid message id %s on line %s",
                $messageId,
                (string)$node->getLineNr()
            );
            return;
        }

        $node->setAttribute('message', Messages::MESSAGE_MAP[$messageId]);
    }

    public function beforeInterval(Interval $node): void
    {
        if ($node->hasAttribute('message')) {
            return;
        }

        if (!isset(Intervals::MESSAGE_MAP[$node->getValue()])) {
            $this->getErrorObject()->addError(
                "Invalid interval %s on line %s",
                $node->getValue(),
                (string)$node->getLineNr()
            );
            return;
        }

        $node->setAttribute('message', Intervals::MESSAGE_MAP[$node->getValue()]);
    }
}
