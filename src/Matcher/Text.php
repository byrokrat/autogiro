<?php

declare(strict_types=1);

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

    public function __construct(int $startPos, string $text)
    {
        parent::__construct($startPos, strlen($text));
        $this->text = $text;
    }

    protected function getDescription(): string
    {
        return "'{$this->text}'";
    }

    protected function isMatch(string $str): bool
    {
        return $str === $this->text;
    }
}
