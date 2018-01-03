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

/**
 * Container of error messages
 */
class ErrorObject
{
    /**
     * @var string[] List of messages describing found errors
     */
    private $errors = [];

    /**
     * Check if any errors have been found
     */
    public function hasErrors(): bool
    {
        return !empty($this->getErrors());
    }

    /**
     * Get list of messages describing found errors
     *
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Add error message to store
     */
    public function addError(string $msg, string ...$args)
    {
        $this->errors[] = sprintf($msg, ...$args);
    }

    /**
     * Reset error store
     */
    public function resetErrors()
    {
        $this->errors = [];
    }
}
