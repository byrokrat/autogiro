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

use byrokrat\autogiro\Visitor;

/**
 * Defines a parse tree processor
 */
class Processor extends Visitor
{
    /**
     * @var string[] List of messages describing found errors
     */
    private $errors = [];

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
     * Check if any errors have been found
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Reset internal error state
     */
    public function resetErrors()
    {
        $this->errors = [];
    }

    /**
     * Add error message to store
     */
    protected function addError(string $msg, string ...$args)
    {
        $this->errors[] = sprintf($msg, ...$args);
    }
}
