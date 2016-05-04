<?php

declare(strict_types=1);

namespace byrokrat\autogiro;

/**
 * Line value object
 */
class Line
{
    /**
     * @var string Line content
     */
    private $string;

    /**
     * @var string Current string encoding
     */
    private $encoding;

    /**
     * Construct line
     *
     * @param string $string   Should not contain new line characters
     * @param string $encoding Will be autodetected if missing
     */
    public function __construct(string $string, string $encoding = '')
    {
        $this->string = $string;
        $this->encoding = $encoding ?: mb_detect_encoding($string);
    }

    /**
     * Get part of line
     *
     * @todo The returned part should always be encoded in utf8
     */
    public function substr(int $startPos, int $length): string
    {
        return substr($this->string, $startPos, $length);
    }

    /**
     * Get line content
     */
    public function __toString(): string
    {
        return $this->string;
    }

    /**
     * Get line encoding
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * Get a new line object converted to encoding
     */
    public function convertTo(string $encoding): Line
    {
        return new static(
            mb_convert_encoding($this->string, $encoding, $this->getEncoding()),
            $encoding
        );
    }

    /**
     * Check if line starts with string
     */
    public function startsWith(string $string): bool
    {
        return 0 === strpos($this->string, $string);
    }

    /**
     * Check if line contains string
     */
    public function contains(string $string): bool
    {
        return false !== strpos($this->string, $string);
    }

    /**
     * Check if line matches regular expression
     */
    public function matches(string $regexp): bool
    {
        return !!preg_match($regexp, $this->string);
    }

    /**
     * Capture parts of line using regular expression
     *
     * @return string[] The captured parts
     */
    public function capture(string $regexp): array
    {
        preg_match($regexp, $this->string, $matches);
        return (array)$matches;
    }

    /**
     * Check if line is empty
     */
    public function isEmpty(): bool
    {
        return !trim($this->string);
    }
}
