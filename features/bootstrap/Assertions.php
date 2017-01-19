<?php

/**
 * Assert methods used in feature context
 */
class Assertions
{
    /**
     * Assert that two variables are equal (===)
     */
    public static function assertEquals($expected, $current)
    {
        if ($expected !== $current) {
            throw new Exception("Unable to assert that '$current' equals '$expected'");
        }
    }

    /**
     * Assert that $needle is an item of $haystack
     */
    public static function assertInArray($needle, array $haystack)
    {
        if (!in_array($needle, $haystack)) {
            throw new Exception("Unable to find '$needle' in array " . var_export($haystack, true));
        }
    }
}
