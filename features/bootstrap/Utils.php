<?php

use byrokrat\autogiro\Enumerator;
use byrokrat\autogiro\Tree\Node;
use Behat\Gherkin\Node\PyStringNode;

/**
 * Utility methods used in feature context
 */
class Utils
{
    /**
     * Extract a node of type $nodeType from $tree
     */
    public static function extractNodeFromTree(string $nodeType, Node $tree): Node
    {
        $enumerator = new Enumerator;

        $return = null;
        $enumerator->on($nodeType, function (Node $node) use (&$return) {
            $return = $node;
        });

        $enumerator->enumerate($tree);

        return $return;
    }

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
