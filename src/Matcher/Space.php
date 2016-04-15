<?php

namespace byrokrat\autogiro\Matcher;

/**
 * Matcher for space characters
 */
class Space extends BaseMatcher
{
    protected function getDescription()
    {
        return 'empty space';
    }

    protected function isMatch($str)
    {
        return preg_match('/^ *$/', $str);
    }
}
