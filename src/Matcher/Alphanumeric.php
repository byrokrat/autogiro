<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Matcher;

/**
 * Matcher for alphanumeric content
 */
class Alphanumeric extends BaseMatcher
{
    protected function getDescription(): string
    {
        return 'alphanumeric';
    }

    protected function isMatch(string $str): bool
    {
        return !!preg_match('/^[A-Za-zÅÄÖåäö 0-9]*$/', $str);
    }
}
