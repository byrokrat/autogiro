<?php

namespace byrokrat\autogiro\Matcher;

use byrokrat\autogiro\Line;
use byrokrat\autogiro\Exception\InvalidContentException;

/**
 * Match part of Line according to specific rules
 */
interface Matcher
{
    /**
     * Match line and grab substring on success
     *
     * @param  Line $line
     * @return string
     * @throws InvalidContentException if line does not match
     */
    public function match(Line $line);
}
