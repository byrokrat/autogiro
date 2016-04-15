<?php

namespace byrokrat\autogiro\Matcher;

/**
 * Matcher for numeric content
 */
class Numeric extends BaseMatcher
{
    protected function getDescription()
    {
        return 'numeric';
    }

    protected function isMatch($str)
    {
        return ctype_digit($str);
    }
}
