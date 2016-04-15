<?php

namespace byrokrat\autogiro\Matcher;

use byrokrat\autogiro\Line;
use byrokrat\autogiro\Exception\InvalidContentException;

/**
 * Grab content of the first valid sub-matcher
 */
class FirstOf implements Matcher
{
    /**
     * @var Matcher[] Loaded matchers
     */
    private $matchers;

    /**
     * Load matchers
     *
     * @param Matcher $matchers Any number of Matcher objects
     */
    public function __construct(Matcher ...$matchers)
    {
        $this->matchers = $matchers;
    }

    /**
     * Match line and grab substring on success
     *
     * @param  Line $line
     * @return string
     * @throws InvalidContentException if line does not match
     */
    public function match(Line $line)
    {
        foreach ($this->matchers as $matcher) {
            try {
                return $matcher->match($line);
            } catch (InvalidContentException $exception) {
            }
        }

        throw new InvalidContentException(
            'Unable to find a match using a set of matchers'
        );
    }
}
