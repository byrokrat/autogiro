<?php

declare(strict_types=1);

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
     * @throws InvalidContentException If line does not match
     */
    public function match(Line $line): string;
}
