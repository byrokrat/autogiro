<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Parser\Matcher;

/**
 * Matcher for space characters
 */
class Space extends BaseMatcher
{
    protected function getDescription(): string
    {
        return 'empty space';
    }

    protected function isMatch(string $str): bool
    {
        return !!preg_match('/^ *$/', $str);
    }
}
