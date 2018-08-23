<?php

use Behat\Gherkin\Node\PyStringNode;

/**
 * Utility methods used in feature context
 */
class Utils
{
    /**
     * Make all lines 80 chars and ending with CRLF (except the last)
     */
    public static function normalize(PyStringNode $node): string
    {
        return rtrim(
            array_reduce($node->getStrings(), function ($carry, $string) {
                return $carry . str_pad(rtrim($string), 80) . "\r\n";
            }),
            "\r\n"
        );
    }
}
