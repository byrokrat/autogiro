<?php

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
    public function __construct($string, $encoding = '')
    {
        $this->string = $string;
        $this->encoding = $encoding ?: mb_detect_encoding($string);
    }

    /**
     * Get part of line
     *
     * @param  int    $startPos
     * @param  int    $length
     * @return string
     *
     * @todo The returned part should always be encoded in utf8
     */
    public function substr($startPos, $length)
    {
        return substr($this->string, $startPos, $length);
    }

    /**
     * Get line content
     *
     * @return string
     */
    public function __tostring()
    {
        return $this->string;
    }

    /**
     * Get line encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Get a new line object converted to encoding
     *
     * @param  string $encoding
     * @return Line
     */
    public function convertTo($encoding)
    {
        return new static(
            mb_convert_encoding($this->string, $encoding, $this->getEncoding()),
            $encoding
        );
    }

    /**
     * Check if line starts with string
     *
     * @param  string $string
     * @return bool
     */
    public function startsWith($string)
    {
        return 0 === strpos($this->string, $string);
    }

    /**
     * Check if line contains string
     *
     * @param  string $string
     * @return bool
     */
    public function contains($string)
    {
        return false !== strpos($this->string, $string);
    }

    /**
     * Check if line matches regular expression
     *
     * @param  string $regexp
     * @return bool
     */
    public function matches($regexp)
    {
        return !!preg_match($regexp, $this->string);
    }

    /**
     * Capture parts of line using regular expression
     *
     * @param  string   $regexp
     * @return string[] The captured parts
     */
    public function capture($regexp)
    {
        preg_match($regexp, $this->string, $matches);
        return (array)$matches;
    }

    /**
     * Check if line is empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return !trim($this->string);
    }
}
