<?php

use byrokrat\autogiro\Parser\Parser;
use byrokrat\autogiro\Parser\ParserFactory;

/**
 * Handles the creation of parsers for feature context
 */
trait ParserTrait
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @Given a parser
     */
    public function aParser()
    {
        $this->parser = (new ParserFactory)->createParser();
    }

    /**
     * @Given a parser that ignores account and id structures
     */
    public function aParserThatIgnoresAccountAndIdStructures()
    {
        $this->parser = (new ParserFactory)->createParser(
            ParserFactory::VISITOR_IGNORE_ACCOUNTS | ParserFactory::VISITOR_IGNORE_IDS
        );
    }

    /**
     * Get the created parser
     */
    protected function getParser(): Parser
    {
        if (!$this->parser) {
            throw new \LogicException("Parser not generated");
        }

        return $this->parser;
    }
}
