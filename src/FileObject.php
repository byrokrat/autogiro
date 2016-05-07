<?php
/**
 * This file is part of byrokrat/autogiro.
 *
 * byrokrat/autogiro is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * byrokrat/autogiro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with byrokrat/autogiro. If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2016 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro;

/**
 * Access object for files from bgc
 *
 * TODO This class is no longer used. Kept during transition.
 */
class FileObject implements \Countable, \IteratorAggregate
{
    /**
     * @var Line[] Contained lines
     */
    private $lines = [];

    /**
     * Create FileObject from raw data
     *
     * @param string $data
     */
    public function __construct(string $data = '')
    {
        if ($data) {
            foreach (preg_split("/(\r\n|\n|\r)/", $data) as $raw) {
                $this->addLine(new Line($raw));
            }
        }
    }

    /**
     * Add a line to file
     */
    public function addLine(Line $line)
    {
        $this->lines[] = $line;
    }

    /**
     * Get contents
     *
     * @param  string $eol      End of line character(s) used
     * @param  string $encoding Encoding used
     */
    public function getContents(string $eol = "\r\n", string $encoding = 'ISO-8859-1'): string
    {
        return array_reduce(
            $this->lines,
            function (string $carry, Line $line) use ($eol, $encoding) {
                return $carry . $line->convertTo($encoding) . $eol;
            },
            ''
        );
    }

    /**
     * Get line from content
     *
     * @throws Exception\RuntimeException If line does not exist
     */
    public function getLine(int $lineNumber): Line
    {
        if (isset($this->lines[$lineNumber])) {
            return $this->lines[$lineNumber];
        }

        throw new Exception\RuntimeException("Line <$lineNumber> does not exist");
    }

    /**
     * Get first line that has content
     *
     * @throws Exception\RuntimeException If no line with content exists
     */
    public function getFirstLine(): Line
    {
        foreach ($this->lines as $line) {
            if (!$line->isEmpty()) {
                return $line;
            }
        }

        throw new Exception\RuntimeException("Non-empty line not found");
    }

    /**
     * Get the last line that has content
     *
     * @throws Exception\RuntimeException If no line with content exists
     */
    public function getLastLine(): Line
    {
        foreach (array_reverse($this->lines) as $line) {
            if (!$line->isEmpty()) {
                return $line;
            }
        }

        throw new Exception\RuntimeException("Non-empty line not found");
    }

    /**
     * Count the number of lines in file
     */
    public function count(): int
    {
        return count($this->lines);
    }

    /**
     * Iterate over lines
     */
    public function getIterator(): \Traversable
    {
        foreach ($this->lines as $lineNumber => $line) {
            yield $lineNumber => $line;
        }
    }
}
