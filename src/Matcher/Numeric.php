<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Matcher;

/**
 * Matcher for numeric content
 */
class Numeric extends BaseMatcher
{
    protected function getDescription(): string
    {
        return 'numeric';
    }

    protected function isMatch(string $str): bool
    {
        return ctype_digit($str);
    }
}
