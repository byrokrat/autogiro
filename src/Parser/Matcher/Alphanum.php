<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Parser\Matcher;

/**
 * Matcher for alphanumeric content
 */
class Alphanum extends BaseMatcher
{
    protected function getDescription(): string
    {
        return 'alphanum';
    }

    protected function isMatch(string $str): bool
    {
        return !!preg_match('/^[A-Za-zÅÄÖåäö0-9 -\/.&]*$/', $str);
    }
}
