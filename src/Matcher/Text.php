<?php

namespace byrokrat\autogiro\Matcher;

/**
 * Matcher for defined text
 */
class Text extends BaseMatcher
{
    /**
     * @var string The text to match
     */
    private $text;

    /**
     * @param int $startPos
     * @param string $text
     */
    public function __construct($startPos, $text)
    {
        parent::__construct($startPos, strlen($text));
        $this->text = $text;
    }

    protected function getDescription()
    {
        return "'{$this->text}'";
    }

    protected function isMatch($str)
    {
        return $str === $this->text;
    }
}
