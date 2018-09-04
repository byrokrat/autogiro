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

use byrokrat\autogiro\MessageRetriever;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\Text;

/**
 * Visitor of message nodes in tree
 *
 * Creates string message as child 'Text'
 */
class MessageVisitor extends ErrorAwareVisitor
{
    /**
     * @var MessageRetriever
     */
    private $messages;

    /**
     * @var string
     */
    private $layoutName = '';

    /**
     * @var string
     */
    private $recordName = '';

    public function __construct(ErrorObject $errorObj, MessageRetriever $messages = null)
    {
        parent::__construct($errorObj);
        $this->messages = $messages ?: new MessageRetriever;
    }

    public function beforeAutogiroFile(Node $node): void
    {
        $this->layoutName = $node->getName();
        $this->recordName = '';
    }

    public function beforeRecord(Node $node): void
    {
        $this->recordName = $node->getName();
    }

    public function beforeMessage(Node $node): void
    {
        $code = (string)$node->getValueFrom('Number');

        $message = $this->messages->readMessage(
            $this->layoutName,
            $this->recordName,
            $node->getName(),
            $code
        );

        if (!$message) {
            $this->getErrorObject()->addError(
                "Invalid message id '%s:%s:%s:%s' on line %s",
                $this->layoutName,
                $this->recordName,
                $node->getName(),
                $code,
                (string)$node->getLineNr()
            );
            return;
        }

        $node->addChild(new Text($node->getLineNr(), $message));
    }
}
