<?php

declare(strict_types=1);

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

    public function __construct(Matcher ...$matchers)
    {
        $this->matchers = $matchers;
    }

    public function match(Line $line): string
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
