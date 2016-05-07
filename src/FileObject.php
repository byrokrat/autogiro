<?php

declare(strict_types=1);

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
