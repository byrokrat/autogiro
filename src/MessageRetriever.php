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

namespace byrokrat\autogiro;

class MessageRetriever
{
    /**
     * Match all wildcard key
     */
    const WILDCARD = '*';

    /**
     * Default location of messages file
     */
    const DEFAULT_MESSAGE_STORE = __DIR__ . '/messages.json';

    /**
     * @var array
     */
    private $messages;

    public function __construct(array $messages = [])
    {
        $this->messages = $messages ?: json_decode(file_get_contents(self::DEFAULT_MESSAGE_STORE), true);
    }

    public function readMessage(string ...$keys): string
    {
        return $this->pickMessage($this->messages, ...$keys);
    }

    private function pickMessage(array $messages, string $key, string ...$additionalKeys): string
    {
        $value = $messages[$key] ?? [];

        if (!empty($additionalKeys)) {
            $value = $this->pickMessage((array)$value, ...$additionalKeys);
        }

        if (!$value && $key != self::WILDCARD) {
            $value = $this->pickMessage($messages, self::WILDCARD, ...$additionalKeys);
        }

        if (!is_scalar($value)) {
            return '';
        }

        return (string)$value;
    }
}
