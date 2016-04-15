<?php

namespace byrokrat\autogiro\Matcher;

use byrokrat\autogiro\Line;
use byrokrat\autogiro\Exception\InvalidContentException;

/**
 * Base implementation of the Matcher interface
 */
abstract class BaseMatcher implements Matcher
{
    /**
     * @var int Start matching from
     */
    private $startPos;

    /**
     * @var int Length to match
     */
    private $length;

    /**
     * Set match area
     *
     * Note that to make matcher definitions and the technical specifications of
     * autoirot appear as similar as possible the first character of a line is
     * re(garded as being in position one (1).
     *
     * @param int $startPos
     * @param int $length
     */
    public function __construct($startPos, $length)
    {
        $this->startPos = $startPos;
        $this->length = $length;
    }

    /**
     * Get a description of the expected content
     *
     * @return string
     */
    abstract protected function getDescription();

    /**
     * Check if string is valid according to matching rules
     *
     * @param  string $str
     * @return bool
     */
    abstract protected function isMatch($str);

    /**
     * Match line and grab substring on success
     *
     * @param  Line $line
     * @return string
     * @throws InvalidContentException if line does not match
     */
    public function match(Line $line)
    {
        $str = $line->substr($this->startPos - 1, $this->length);

        if (!$this->isMatch($str)) {
            throw new InvalidContentException(
                sprintf(
                    "Invalid content '%s' (should be %s) at position %s",
                    $str,
                    $this->getDescription(),
                    $this->startPos - 1
                )
            );
        }

        return $str;
    }
}
