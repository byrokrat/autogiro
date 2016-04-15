<?php

namespace byrokrat\autogiro\Matcher;

/**
 * Matcher for alphanumeric content
 */
class Alphanumeric extends BaseMatcher
{
    protected function getDescription()
    {
        return 'alphanumeric';
    }

    protected function isMatch($str)
    {
        return preg_match('/^[A-Za-zÅÄÖåäö 0-9]*$/', $str);
    }
}
