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

namespace byrokrat\autogiro\Message;

use byrokrat\autogiro\Exception\RuntimeException;

/**
 * Simplifies the creation of message objects
 */
class MessageFactory
{
    /**
     * Create message identified by message id
     */
    public function createMessage(string $messageId): Message
    {
        if (isset(Messages::MESSAGE_MAP[$messageId])) {
            return new Message($messageId, Messages::MESSAGE_MAP[$messageId]);
        }

        throw new RuntimeException("Unknown message id $messageId");
    }
}
