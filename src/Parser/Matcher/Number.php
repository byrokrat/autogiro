<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Parser\Matcher;

/**
 * Matcher for numeric content
 */
class Number extends BaseMatcher
{
    protected function getDescription(): string
    {
        return 'number';
    }

    protected function isMatch(string $str): bool
    {
        return ctype_digit($str);
    }
}
